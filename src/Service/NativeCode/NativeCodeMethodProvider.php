<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Context\TypedValue;
use Cast\Model\Context\VariableValuePair;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\NativeCode\PhpSubtypeValue;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\NativeCodeExpression;
use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Literal\RealLiteral;
use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\EnumerationValueType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\ClosureValue;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\MutableValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Execution\Flow\ThrowResult;
use Cast\Service\Expression\AnalyserExpressionSource;
use Cast\Service\Expression\ExecutorException;
use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Registry\NoCastAvailable;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\ValueToTypeConverter;
use Cast\Service\Type\ValueTypeResolver;
use Cast\Service\Type\ValueUpCaster;
use Cast\Service\Value\PhpToCastWithSubtypesValueTransformer;
use JsonException;
use PDO;
use PDOException;

final readonly class NativeCodeMethodProvider {
	public function __construct(
		private TypeAssistant $typeAssistant,
		private ValueTypeResolver $valueTypeResolver,
		private SubtypeRelationChecker $subtypeRelationChecker,
		private ValueToTypeConverter $valueToTypeConverter,
		private PhpToCastWithSubtypesValueTransformer $phpToCastWithSubtypesValueTransformer,
		private CastRegistry $castRegistry,
		private ValueUpcaster $valueUpcaster,
		private FunctionExecutor $functionExecutor,
		private AnalyserExpressionSource $analyserExpressionSource,
		private HydratorFactory $hydratorFactory,
		private DehydratorFactory $deydratorFactory,
		private ContainerFactory $containerFactory,
	) {}

	private function buildNativeMethod(
		callable $callback,
		bool $passParameter = true,
		bool $convertTargetToBaseType = false
	): CallableNativeCodeHandler {
		return new CallableNativeCodeHandler(fn(
			Value                 $target,
			Value                 $parameter,
			VariableValueScope    $variableValueScope,
			SubExpressionExecutor $subExpressionExecutor
		): ExecutionResultValueContext => new ExecutionResultValueContext(
			$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast(
				$callback(... [
					$this->phpToCastWithSubtypesValueTransformer->fromCastToPhp(
						$convertTargetToBaseType ?
							$this->convertToBaseValue($target) : $target
					)
				],
					... $passParameter ?
						[$this->phpToCastWithSubtypesValueTransformer->fromCastToPhp($parameter)] : []
				)
			),
			$variableValueScope
		));
	}

	private function resolvedType(Type $type): Type {
		return $this->typeAssistant->followAliases($type);
	}

	private function convertToBaseValue(Value $value): Value {
		if ($value instanceof SubtypeValue) {
			return $this->convertToBaseValue($value->baseValue);
		}
		if ($value instanceof EnumerationValue) {
			return new LiteralValue(new StringLiteral($value->enumValue->identifier));
		}
		return $value;
	}

	private function toBaseValue(array|PhpSubtypeValue $value): mixed {
		$v = $value;
		while($v instanceof PhpSubtypeValue) {
			$v = $v->baseValue;
		}
		return $v;
	}

	private function typeRef(TypeNameIdentifier|string $name): Type {
		$name = $name instanceof TypeNameIdentifier ? $name : new TypeNameIdentifier($name);
		return $this->typeAssistant->typeByName($name);
	}

	public function getMethods(): array {
		return [
			'PRINT' => $this->buildNativeMethod(
				function(string $target): string {
					print($target);
					return $target;
				}
			),
			'DEBUG' => new CallableNativeCodeHandler(function(
				Value              $target,
				Value              $parameter,
				VariableValueScope $variableValueScope,
			): ExecutionResultValueContext {
				print("\nDEBUG: $target\n");
				return new ExecutionResultValueContext(
					$target,
					$variableValueScope
				);
			}),
			'Any::binaryEqual' => new CallableNativeCodeHandler(fn(
				Value                 $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext => new ExecutionResultValueContext(
				new LiteralValue (new BooleanLiteral(
					(string)$target === (string)$parameter //@TODO - fix
				)),
				$variableValueScope
			)),
			'Any::binaryNotEqual' => new CallableNativeCodeHandler(fn(
				Value                 $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext => new ExecutionResultValueContext(
				new LiteralValue (new BooleanLiteral(
					(string)$target !== (string)$parameter //@TODO - fix
				)),
				$variableValueScope
			)),
			'Any::isSubtypeOf' => new CallableNativeCodeHandler(fn(
				Value                 $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext => new ExecutionResultValueContext(
				new LiteralValue(new BooleanLiteral(
					$this->subtypeRelationChecker->isSubtype(
						$this->valueTypeResolver->findTypeOf($target),
						$this->valueToTypeConverter->tryConvertValueToType($parameter)
					),
				)),
				$variableValueScope
			)),
			'Container::instanceOf' => new CallableNativeCodeHandler(function(
				SubtypeValue                 $target,
				TypeValue             $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor,
				Expression            $expression,
				Type                  $targetType
			): ExecutionResultValueContext {
				try {
					/** @var ListValue $configuration */
					$configuration = $target->baseValue->items['cfg'];

					$value = $this->containerFactory
						->getContainer(
							$configuration,
							$subExpressionExecutor,
							$target
						)
						->instanceOf($parameter->type);
				} catch (ContainerException $exception) {
					throw new ThrowResult(
						new SubtypeValue(
							new TypeNameIdentifier("ContainerError"),
							new DictValue([
								'type' => $parameter,
								'message' => $exception->errorMessage
							])
						)
					);
				}
				return new ExecutionResultValueContext(
					$value,
					$variableValueScope
				);
			}),
			'Any::as' => new CallableNativeCodeHandler(function(
				Value                 $target,
				TypeValue             $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor,
				Expression            $expression,
				Type                  $targetType
			): ExecutionResultValueContext {
				$val = $variableValueScope->valueOf(new VariableNameIdentifier('$'));
				$fromType = $targetType;
				if ($fromType instanceof AnyType || $fromType instanceof UnionType) {
					$fromType = $this->valueTypeResolver->findTypeOf(
						$val = $variableValueScope->valueOf(new VariableNameIdentifier('$'))
					);
				}
				$toType = $this->typeAssistant->closestTypeName($parameter->type);
				$castValue = NoCastAvailable::value;
				foreach($this->typeAssistant->allNamedTypes($fromType) as $candidateFromType) {
					$castValue = $this->castRegistry->getCast($candidateFromType, $toType);
					if ($castValue !== NoCastAvailable::value) {
						break;
					}
				}
				if ($castValue === NoCastAvailable::value && $this->subtypeRelationChecker->isSubtype(
					$fromType, $parameter->type
				)) {
					return new ExecutionResultValueContext(
						$this->valueUpcaster->upcastValue(
							$val,
							$this->typeAssistant->followAliases($parameter->type)
						),
						$variableValueScope
					);
				}
				/*if ($fromType::class === $toType::class) {
					return new ExecutionResultValueContext($val, $variableValueScope);
				}*/
				/*if ($val instanceof TupleConstant) {
					$fromType = new ArrayType(new AnyType, new LengthRange(0, PlusInfinity::value));
				}
				if ($fromType instanceof IntegerType) {
					$fromType = IntegerType::base();
				}*/
				/*if ($castValue === NoCastAvailable::value) {
					$castValue = $this->castRegistry->getCast(new AnyType, $toType);
				}*/
				if ($castValue === NoCastAvailable::value) {
					$targetRuntimeType = $this->valueTypeResolver->findTypeOf($target);
					if ($this->subtypeRelationChecker->isSubtype($targetRuntimeType, $parameter->type)) {
						return new ExecutionResultValueContext(
							$target,
							$variableValueScope
						);
					}
					throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("NoCastAvailable"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast([
							'value' => $target, 'targetType' => (string)$parameter
						])
					));
				}
				$value = $this->functionExecutor->executeCastFunction(
					$subExpressionExecutor,
					$castValue,
					$target
				);
				return new ExecutionResultValueContext($value, $variableValueScope);
			}),
			'Any::type' => new CallableNativeCodeHandler(fn(
				Value                 $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext => new ExecutionResultValueContext(
				new TypeValue($this->valueTypeResolver->findTypeOf($target)),
				$variableValueScope
			)),
			'Any::compilationType' => new CallableNativeCodeHandler(fn(
				Value                 $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor,
				Expression            $expression,
				Type                  $targetType,
				Type                  $parameterType
			): ExecutionResultValueContext => new ExecutionResultValueContext(
				new TypeValue($targetType),
				$variableValueScope
			)),
			'Numeric::binaryLessThan' => $this->buildNativeMethod(fn(int|float $target, int|float $parameter): bool
				=> $target < $parameter),
			'Numeric::binaryLessThanEqual' => $this->buildNativeMethod(fn(int|float $target, int|float $parameter): bool
				=> $target <= $parameter),
			'Numeric::binaryGreaterThan' => $this->buildNativeMethod(fn(int|float $target, int|float $parameter): bool
				=> $target > $parameter),
			'Numeric::binaryGreaterThanEqual' => $this->buildNativeMethod(fn(int|float $target, int|float $parameter): bool
				=> $target >= $parameter),
			'Integer::binaryBitwiseOr' => $this->buildNativeMethod(fn(int $target, int $parameter): int
				=> $target | $parameter),
			'Integer::binaryBitwiseAnd' => $this->buildNativeMethod(fn(int $target, int $parameter): int
				=> $target & $parameter),
			'Integer::binaryBitwiseXor' => $this->buildNativeMethod(fn(int $target, int $parameter): int
				=> $target ^ $parameter),
			'Integer::unaryBitwiseNot' => $this->buildNativeMethod(fn(int $target): int
				=> ~ $target),
			'Boolean::binaryLogicOr' => $this->buildNativeMethod(fn(bool $target, bool $parameter): bool
				=> $target || $parameter),
			'Boolean::binaryLogicAnd' => $this->buildNativeMethod(fn(bool $target, bool $parameter): bool
				=> $target && $parameter),
			'Boolean::unaryLogicNot' => $this->buildNativeMethod(fn(bool $target): bool
				=> ! $target),
			'String::length' => $this->buildNativeMethod(strlen(...), false, true),
			'String::reverse' => $this->buildNativeMethod(strrev(...), false, true),
			'String::toLowerCase' => $this->buildNativeMethod(strtolower(...), false, true),
			'String::toUpperCase' => $this->buildNativeMethod(strtoupper(...), false, true),
			'String::trim' => $this->buildNativeMethod(trim(...), false, true),
			'String::trimLeft' => $this->buildNativeMethod(ltrim(...), false, true),
			'String::trimRight' => $this->buildNativeMethod(rtrim(...), false, true),
			'String::contains' => $this->buildNativeMethod(str_contains(...), true),
			'String::startsWith' => $this->buildNativeMethod(str_starts_with(...), true),
			'String::endsWith' => $this->buildNativeMethod(str_ends_with(...), true),
			'String::substringLength' => $this->buildNativeMethod(
				fn(string $target, array $parameter): string =>
					substr($target, $parameter['start'], $parameter['length'])
			),
			'String::substringTo' => $this->buildNativeMethod(
				fn(string $target, array $parameter): string =>
					substr($target, $parameter['start'], max(0,
						$parameter['end'] - $parameter['start']
					))
			),
			'String::positionOf' => $this->buildNativeMethod(
				fn(string $target, string $parameter): int =>
					($pos = strpos($target, $parameter)) !== false ? $pos :
					throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("SubstringNotInString"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast([
							'string' => $target, 'substring' => $parameter
						])
					))
			),
			'String::lastPositionOf' => $this->buildNativeMethod(
				fn(string $target, string $parameter): int =>
					($pos = strrpos($target, $parameter)) !== false ? $pos :
					throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("SubstringNotInString"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast([
							'string' => $target, 'substring' => $parameter
						])
					))
			),
			'String::concat' => $this->buildNativeMethod(
				fn(string $target, string $parameter): string => $target . $parameter
			),
			'String::concatList' => $this->buildNativeMethod(
				fn(string $target, array $parameter): string => $target . implode($parameter)
			),
			'String::split' => $this->buildNativeMethod(
				fn(string $target, string $parameter): array => explode($parameter, $target)
			),
			'String::chunk' => $this->buildNativeMethod(str_split(...)),
			'String::padLeft' => $this->buildNativeMethod(
				fn(string $target, array $parameter): string => str_pad(
					$target, $parameter['length'], $parameter['padString'], STR_PAD_LEFT
				)
			),
			'String::padRight' => $this->buildNativeMethod(
				fn(string $target, array $parameter): string => str_pad(
					$target, $parameter['length'], $parameter['padString']
				)
			),
			'Array::asMap' => $this->buildNativeMethod(
				function(array $target): array {
					$result = [];
					foreach($target as [$key, $value]) {
						$result[$key] = $value;
					}
					return $result;
				}
			),
			'Array::length' => $this->buildNativeMethod(count(...), false),
			'Array::reverse' => $this->buildNativeMethod(array_reverse(...), false, true),
			'Array::item' => $this->buildNativeMethod(
				fn(array|PhpSubtypeValue $target, int $parameter): mixed =>
					($target = $this->toBaseValue($target)) &&
					array_key_exists($parameter, $target) ? $target[$parameter] :
					throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("ArrayItemNotFound"),
						new LiteralValue(new IntegerLiteral($parameter))
					))
			),
			'Array::combineAsText' => $this->buildNativeMethod(
				fn(array $target, string $parameter): string => implode($parameter, $target),
				convertTargetToBaseType: true
			),
			'Array::withoutFirst' => $this->buildNativeMethod(
				fn(array $target): array => count($target) ?
					['element' => array_shift($target), 'array' => $target]
					: throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("ArrayItemNotFound"),
						new LiteralValue(new NullLiteral)
					))
			),
			'Array::withoutLast' => $this->buildNativeMethod(
				fn(array $target): array => count($target) ?
					['element' => array_pop($target), 'array' => $target]
					: throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("ArrayItemNotFound"),
						new LiteralValue(new NullLiteral)
					))
			),
			'Array::contains' => $this->buildNativeMethod(
				fn(array $target, mixed $parameter): bool =>
					in_array($parameter, $target, true)
			),
			'Array::indexOf' => $this->buildNativeMethod(
				fn(array $target, mixed $parameter): int =>
					($key = array_search($parameter, $target, true)) !== false ? $key :
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("ArrayItemNotFound"),
							$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($parameter)
						))
			),
			'Array::without' => $this->buildNativeMethod(
				function(array $target, mixed $parameter): array {
					$key = array_search($parameter, $target, true);
					if ($key === false) {
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("ArrayItemNotFound"),
							$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($parameter)
						));
					}
					array_splice($target, $key, 1);
					return $target;
				}
			),
			'Array::withoutByIndex' => $this->buildNativeMethod(
				function(array $target, int $parameter): array {
					if (!array_key_exists($parameter, $target)) {
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("ArrayItemNotFound"),
							new LiteralValue(new IntegerLiteral($parameter))
						));
					}
					array_splice($target, $parameter, 1);
					return $target;
				}
			),
			'Array::sortAsNumber' => $this->buildNativeMethod(
				function(array $target): array {
					sort($target, SORT_NUMERIC);
					return $target;
				}
			),
			'Array::sortAsString' => $this->buildNativeMethod(
				function(array $target): array {
					sort($target, SORT_STRING);
					return $target;
				}
			),
			'Array::uniqueAsNumber' => $this->buildNativeMethod(
				function(array $target): array {
					return array_values(array_unique($target, SORT_NUMERIC));
				}
			),
			'Array::uniqueAsString' => $this->buildNativeMethod(
				function(array $target): array {
					return array_values(array_unique($target, SORT_STRING));
				}
			),
			'Array::appendWith' => $this->buildNativeMethod(array_merge(...)),
			'Array::pad' => $this->buildNativeMethod(
				function(array $target, array $parameter): array {
					return array_pad($target, $parameter['length'], $parameter['value']);
				}
			),
			'Array::insertLast' => $this->buildNativeMethod(
				function(array $target, mixed $parameter): array {
					$target[] = $parameter;
					return $target;
				}
			),
			'Array::insertFirst' => $this->buildNativeMethod(
				function(array $target, mixed $parameter): array {
					array_unshift($target, $parameter);
					return $target;
				}
			),
			'Array::countValues' => $this->buildNativeMethod(array_count_values(...), false),
			'Array::flip' => $this->buildNativeMethod(array_flip(...), false),
			'Array::sum' => $this->buildNativeMethod(array_sum(...), false),
			'Array::min' => $this->buildNativeMethod(min(...), false),
			'Array::max' => $this->buildNativeMethod(max(...), false),
			'Array::map' => new CallableNativeCodeHandler(function(
				ListValue                  $target,
				ClosureValue|FunctionValue $parameter,
				VariableValueScope         $variableValueScope,
				SubExpressionExecutor      $subExpressionExecutor
			): ExecutionResultValueContext {
				$result = [];
				foreach($target->items as $key => $value) {
					$result[$key] = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$parameter,
						$value
					);
				}
				return new ExecutionResultValueContext(
					new ListValue(... $result),
					$variableValueScope
				);
			}),
			'Array::filter' => new CallableNativeCodeHandler(function(
				ListValue                  $target,
				ClosureValue|FunctionValue $parameter,
				VariableValueScope         $variableValueScope,
				SubExpressionExecutor      $subExpressionExecutor
			): ExecutionResultValueContext {
				$result = [];
				foreach($target->items as $value) {
					$b = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$parameter,
						$value
					);
					if ($b instanceof LiteralValue &&
						$b->literal instanceof BooleanLiteral &&
						$b->literal->value === true
					) {
						$result[] = $value;
					}
				}
				return new ExecutionResultValueContext(
					new ListValue(... $result),
					$variableValueScope
				);
			}),
			'Array::findFirst' => new CallableNativeCodeHandler(function(
				ListValue                  $target,
				ClosureValue|FunctionValue $parameter,
				VariableValueScope         $variableValueScope,
				SubExpressionExecutor      $subExpressionExecutor
			): ExecutionResultValueContext {
				foreach($target->items as $value) {
					$b = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$parameter,
						$value
					);
					if ($b instanceof LiteralValue &&
						$b->literal instanceof BooleanLiteral &&
						$b->literal->value === true
					) {
						return new ExecutionResultValueContext($value, $variableValueScope);
					}
				}
				throw new ThrowResult(new SubtypeValue(
					new TypeNameIdentifier("ArrayItemNotFound"),
					new LiteralValue(new NullLiteral)
				));
			}),
			'Map::item' => $this->buildNativeMethod(
				fn(array $target, string $parameter): mixed =>
					array_key_exists($parameter, $target) ? $target[$parameter] :
					throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("MapItemNotFound"),
						new LiteralValue(new StringLiteral($parameter))
					)),
				convertTargetToBaseType: true
			),
			'Map::keyExists' => $this->buildNativeMethod(
				fn(array $target, string $parameter): bool => array_key_exists($parameter, $target)
			),
			'Map::contains' => $this->buildNativeMethod(
				fn(array $target, mixed $parameter): bool =>
					in_array($parameter, $target, true)
			),
			'Map::keyOf' => $this->buildNativeMethod(
				fn(array $target, mixed $parameter): string =>
					($key = array_search($parameter, $target, true)) !== false ? $key :
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("MapItemNotFound"),
							$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($parameter)
						)),
				convertTargetToBaseType: true
			),
			'Map::keys' => $this->buildNativeMethod(array_keys(...), false, true),
			'Map::values' => $this->buildNativeMethod(array_values(...), false, true),
			'Map::flip' => $this->buildNativeMethod(array_flip(...), false, true),
			'Map::map' => new CallableNativeCodeHandler(function(
				DictValue                  $target,
				ClosureValue|FunctionValue $parameter,
				VariableValueScope         $variableValueScope,
				SubExpressionExecutor      $subExpressionExecutor
			): ExecutionResultValueContext {
				$result = [];
				foreach($target->items as $key => $value) {
					$result[$key] = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$parameter,
						$value
					);
				}
				return new ExecutionResultValueContext(
					new DictValue($result),
					$variableValueScope
				);
			}),
			'Map::mapKeyValue' => new CallableNativeCodeHandler(function(
				DictValue                  $target,
				ClosureValue|FunctionValue $parameter,
				VariableValueScope         $variableValueScope,
				SubExpressionExecutor      $subExpressionExecutor
			): ExecutionResultValueContext {
				$result = [];
				foreach($target->items as $key => $value) {
					$value = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$parameter,
						new DictValue([
							'key' => new LiteralValue(new StringLiteral($key)),
							'value' => $value
						])
					);
					$result[$this->phpToCastWithSubtypesValueTransformer->fromCastToPhp($value->items['key'])] = $value->items['value'];
				}
				return new ExecutionResultValueContext(
					new DictValue($result),
					$variableValueScope
				);
			}),
			'Map::filter' => new CallableNativeCodeHandler(function(
				DictValue                  $target,
				ClosureValue|FunctionValue $parameter,
				VariableValueScope         $variableValueScope,
				SubExpressionExecutor      $subExpressionExecutor
			): ExecutionResultValueContext {
				$result = [];
				foreach($target->items as $key => $value) {
					$b = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$parameter,
						$value
					);
					if ($b instanceof LiteralValue &&
						$b->literal instanceof BooleanLiteral &&
						$b->literal->value === true
					) {
						$result[$key] = $value;
					}
				}
				return new ExecutionResultValueContext(
					new DictValue($result),
					$variableValueScope
				);
			}),
			'Map::filterKeyValue' => new CallableNativeCodeHandler(function(
				DictValue                  $target,
				ClosureValue|FunctionValue $parameter,
				VariableValueScope         $variableValueScope,
				SubExpressionExecutor      $subExpressionExecutor
			): ExecutionResultValueContext {
				$result = [];
				foreach($target->items as $key => $value) {
					$b = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$parameter,
						new DictValue([
							'key' => new LiteralValue(new StringLiteral($key)),
							'value' => $value
						])
					);
					if ($b instanceof LiteralValue &&
						$b->literal instanceof BooleanLiteral &&
						$b->literal->value === true
					) {
						$result[$key] = $value;
					}
				}
				return new ExecutionResultValueContext(
					new DictValue($result),
					$variableValueScope
				);
			}),
			'Map::findFirst' => new CallableNativeCodeHandler(function(
				DictValue                  $target,
				ClosureValue|FunctionValue $parameter,
				VariableValueScope         $variableValueScope,
				SubExpressionExecutor      $subExpressionExecutor
			): ExecutionResultValueContext {
				foreach($target->items as $value) {
					$b = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$parameter,
						$value
					);
					if ($b instanceof LiteralValue &&
						$b->literal instanceof BooleanLiteral &&
						$b->literal->value === true
					) {
						return new ExecutionResultValueContext($value, $variableValueScope);
					}
				}
				throw new ThrowResult(new SubtypeValue(
					new TypeNameIdentifier("MapItemNotFound"),
					new LiteralValue(new NullLiteral)
				));
			}),
			'Map::findFirstKeyValue' => new CallableNativeCodeHandler(function(
				DictValue                  $target,
				ClosureValue|FunctionValue $parameter,
				VariableValueScope         $variableValueScope,
				SubExpressionExecutor      $subExpressionExecutor
			): ExecutionResultValueContext {
				foreach($target->items as $key => $value) {
					$ret = new DictValue([
						'key' => new LiteralValue(new StringLiteral($key)),
						'value' => $value
					]);
					$b = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$parameter,
						$ret
					);
					if ($b instanceof LiteralValue &&
						$b->literal instanceof BooleanLiteral &&
						$b->literal->value === true
					) {
						return new ExecutionResultValueContext($ret, $variableValueScope);
					}
				}
				throw new ThrowResult(new SubtypeValue(
					new TypeNameIdentifier("MapItemNotFound"),
					new LiteralValue(new NullLiteral)
				));
			}),
			'Map::withKeyValue' => $this->buildNativeMethod(
				function(array $target, array $parameter): array {
					$target[$parameter['key']] = $parameter['value'];
					return $target;
				},
				convertTargetToBaseType: true
			),
			'Map::mergeWith' => $this->buildNativeMethod(
				function(array $target, array $parameter): array {
					return $target + $parameter;
				},
				convertTargetToBaseType: true
			),
			'Map::without' => $this->buildNativeMethod(
				function(array $target, mixed $parameter): array {
					$key = array_search($parameter, $target, true);
					if ($key === false) {
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("MapItemNotFound"),
							$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($parameter)
						));
					}
					unset($target[$key]);
					return $target;
				},
				convertTargetToBaseType: true
			),
			'Map::withoutByKey' => $this->buildNativeMethod(
				function(array $target, string $parameter): array {
					if (!array_key_exists($parameter, $target)) {
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("MapItemNotFound"),
							new LiteralValue(new StringLiteral($parameter))
						));
					}
					unset($target[$parameter]);
					return $target;
				},
				convertTargetToBaseType: true
			),
			'Integer::upTo' => $this->buildNativeMethod(
				fn(int $target, int $parameter): array =>
					$parameter >= $target ? range($target, $parameter, 1) : []
			),
			'Integer::downTo' => $this->buildNativeMethod(
				fn(int $target, int $parameter): array =>
					$parameter <= $target ? range($target, $parameter, -1) : []
			),
			'Integer::plus' => $this->buildNativeMethod(
				fn(int $target, int $parameter): int => $target + $parameter
			),
			'Integer::plusReal' => $this->buildNativeMethod(
				fn(int $target, float $parameter): float => $target + $parameter
			),
			'Real::plus' => $this->buildNativeMethod(
				fn(float $target, float $parameter): float => $target + $parameter
			),

			'Integer::binaryModulo' => $this->buildNativeMethod(
				fn(int $target, int $parameter): int => $target % $parameter
			),
			'Integer::minus' => $this->buildNativeMethod(
				fn(int $target, int $parameter): int => $target - $parameter
			),
			'Integer::minusReal' => $this->buildNativeMethod(
				fn(int $target, float $parameter): float => $target - $parameter
			),
			'Real::minus' => $this->buildNativeMethod(
				fn(float $target, float $parameter): float => $target - $parameter
			),

			'Integer::multiply' => $this->buildNativeMethod(
				fn(int $target, int $parameter): int => $target * $parameter
			),
			'Integer::multiplyReal' => $this->buildNativeMethod(
				fn(int $target, float $parameter): float => $target * $parameter
			),
			'Real::multiply' => $this->buildNativeMethod(
				fn(float $target, float $parameter): float => $target * $parameter
			),

			'Integer::power' => $this->buildNativeMethod(
				fn(int $target, int $parameter): int => $target ** $parameter
			),
			'Integer::powerReal' => $this->buildNativeMethod(
				fn(int $target, float $parameter): float => $target ** $parameter
			),
			'Real::power' => $this->buildNativeMethod(
				fn(float $target, float $parameter): float => $target ** $parameter
			),

			'Integer::divide' => $this->buildNativeMethod(
				fn(int $target, int $parameter): float => $parameter !== 0 ?
					$target / $parameter : throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("DivisionByZero"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($target)
					))
			),
			'Integer::divideReal' => $this->buildNativeMethod(
				fn(int $target, float $parameter): float => $parameter !== 0.0 ?
					$target / $parameter : throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("DivisionByZero"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($target)
					))
			),
			'Real::divide' => $this->buildNativeMethod(
				fn(float $target, float $parameter): float => $parameter !== 0.0 ?
					$target / $parameter : throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("DivisionByZero"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($target)
					))
			),

			'Integer::integerDivide' => $this->buildNativeMethod(
				fn(int $target, int $parameter): int => $parameter !== 0 ?
					intdiv($target, $parameter) : throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("DivisionByZero"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($target)
					))
			),

			'Integer::modulo' => $this->buildNativeMethod(
				fn(int $target, int $parameter): int => $parameter !== 0 ?
					$target % $parameter : throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("DivisionByZero"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($target)
					))
			),
			'Integer::moduloReal' => $this->buildNativeMethod(
				fn(int $target, float $parameter): float => $parameter !== 0.0 ?
					fmod($target, $parameter) : throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("DivisionByZero"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($target)
					))
			),
			'Real::modulo' => $this->buildNativeMethod(
				fn(float $target, float $parameter): float => $parameter !== 0.0 ?
					fmod($target, $parameter) : throw new ThrowResult(new SubtypeValue(
						new TypeNameIdentifier("DivisionByZero"),
						$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($target)
					))
			),

			'Integer::unaryMinus' => $this->buildNativeMethod(fn(int $target): int => -$target),
			'Integer::unaryPlus' => $this->buildNativeMethod(fn(int $target): int => +$target),
			'Real::unaryMinus' => $this->buildNativeMethod(fn(float $target): float => -$target),
			'Real::unaryPlus' => $this->buildNativeMethod(fn(float $target): float => +$target),
			'Real::asInteger' => $this->buildNativeMethod(fn(float $target): int => (int)$target),
			'Real::roundAsInteger' => $this->buildNativeMethod(fn(float $target): int => (int)round($target)),
			'Real::roundAsDecimal' => $this->buildNativeMethod(fn(float $target, int $parameter): float => round($target, $parameter)),
			'Real::floor' => $this->buildNativeMethod(fn(float $target): int => floor($target)),
			'Real::ceil' => $this->buildNativeMethod(fn(float $target): int => ceil($target)),

			'String::jsonStringToValue' => $this->buildNativeMethod(
				function(string $target): null|bool|int|float|string|array|object {
					try {
						return json_decode($target, flags: JSON_THROW_ON_ERROR);
					} catch (JsonException) {
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("InvalidJsonValue"),
							$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast($target)
						));
					}
				}
			),
			'JsonValue::jsonValueToString' => $this->buildNativeMethod(
				fn(null|bool|int|float|string|array|object $target): string =>
					json_encode($target, JSON_PRETTY_PRINT)
			),

			'Any::asJsonString' => new CallableNativeCodeHandler(function(
				Value $target,
				Value $parameter,
				VariableValueScope $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor,
				Expression            $expression,
				Type                  $targetType,
				Type                  $parameterType
			): ExecutionResultValueContext {
				$value = $subExpressionExecutor->execute(
					new NativeCodeExpression(
						'Any::asJsonValue', $parameterType),
					$variableValueScope
				)->value;
				$value = $subExpressionExecutor->execute(
					new NativeCodeExpression(
						'JsonValue::jsonValueToString', $parameterType),
					$variableValueScope->withAddedValues(
						new VariableValuePair(
							new VariableNameIdentifier('$'),
							new TypedValue(
								$parameterType,
								$value
							)
						)
					)
				)->value;
				return new ExecutionResultValueContext(
					$value,
					$variableValueScope
				);
			}),
			'Any::asJsonValue' => new CallableNativeCodeHandler(function(
				Value $target,
				Value $parameter,
				VariableValueScope $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext {
				try {
					$value = $this->deydratorFactory->getDehydrator($subExpressionExecutor)
						->dehydrate($target, "value");
				} catch (DehydrationException $exception) {
					throw new ThrowResult(
						new SubtypeValue(
							new TypeNameIdentifier("DehydrationFailed"),
							new DictValue([
								'value' => $exception->value,
								'path' => new LiteralValue(new StringLiteral($exception->hydrationPath)),
								'message' => new LiteralValue(new StringLiteral($exception->errorMessage)),
							])
						)
					);
				}
				return new ExecutionResultValueContext(
					$value,
					$variableValueScope
				);
			}),
			'String::hydrateJsonAs' => new CallableNativeCodeHandler(function(
				Value $target,
				Value $parameter,
				VariableValueScope $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor,
				Expression            $expression,
				Type                  $targetType,
				Type                  $parameterType
			): ExecutionResultValueContext {
				$value = $subExpressionExecutor->execute(
					new NativeCodeExpression(
						'String::jsonStringToValue', $parameterType),
					$variableValueScope
				)->value;
				$value = $subExpressionExecutor->execute(
					new NativeCodeExpression(
						'JsonValue::hydrateAs', $parameterType),
					$variableValueScope->withAddedValues(
						new VariableValuePair(
							new VariableNameIdentifier('$'),
							new TypedValue(
								$parameterType,
								$value
							)
						)
					)
				)->value;
				return new ExecutionResultValueContext(
					$value,
					$variableValueScope
				);
			}),
			'JsonValue::hydrateAs' => new CallableNativeCodeHandler(function(
				Value $target,
				TypeValue $parameter,
				VariableValueScope $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($parameter->type);
				try {
					$value = $this->hydratorFactory
						->getHydrator($subExpressionExecutor)
						->hydrate($target, $resolvedType, "value");
				} catch (HydrationException $exception) {
					throw new ThrowResult(
						new SubtypeValue(
							new TypeNameIdentifier("HydrationFailed"),
							new DictValue([
								'value' => $exception->value,
								'path' => new LiteralValue(new StringLiteral($exception->hydrationPath)),
								'message' => new LiteralValue(new StringLiteral($exception->errorMessage)),
							])
						)
					);
				}
				return new ExecutionResultValueContext(
					$value,
					$variableValueScope
				);
			}),

			'Type::String::minLength' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					new LiteralValue(new IntegerLiteral($resolvedType->range->minLength)),
					$variableValueScope
				);
			}),

			'Type::String::maxLength' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					$resolvedType->range->maxLength === PlusInfinity::value ?
						new SubtypeValue(new TypeNameIdentifier("PlusInfinity"),
							new LiteralValue(new NullLiteral)
						) :
						new LiteralValue(new IntegerLiteral($resolvedType->range->maxLength)),
					$variableValueScope
				);
			}),

			'Mutable::value' => new CallableNativeCodeHandler(function(
				MutableValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				return new ExecutionResultValueContext(
					$target->targetValue,
					$variableValueScope
				);
			}),

			'Mutable::SET' => new CallableNativeCodeHandler(function(
				MutableValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$target->targetValue = $parameter;
				return new ExecutionResultValueContext(
					$target->targetValue,
					$variableValueScope
				);
			}),

			'Type::Type::refType' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					new TypeValue($resolvedType->refType),
					$variableValueScope
				);
			}),

			'Type::Map::itemType' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					new TypeValue($resolvedType->itemType),
					$variableValueScope
				);
			}),

			'Type::Map::minLength' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					new LiteralValue(new IntegerLiteral($resolvedType->range->minLength)),
					$variableValueScope
				);
			}),

			'Type::Map::maxLength' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					$resolvedType->range->maxLength === PlusInfinity::value ?
						new SubtypeValue(new TypeNameIdentifier("PlusInfinity"),
							new LiteralValue(new NullLiteral)
						) :
						new LiteralValue(new IntegerLiteral($resolvedType->range->maxLength)),
					$variableValueScope
				);
			}),

			'Type::Array::itemType' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					new TypeValue($resolvedType->itemType),
					$variableValueScope
				);
			}),

			'Type::Array::minLength' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					new LiteralValue(new IntegerLiteral($resolvedType->range->minLength)),
					$variableValueScope
				);
			}),

			'Type::Array::maxLength' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					$resolvedType->range->maxLength === PlusInfinity::value ?
						new SubtypeValue(new TypeNameIdentifier("PlusInfinity"),
							new LiteralValue(new NullLiteral)
						) :
						new LiteralValue(new IntegerLiteral($resolvedType->range->maxLength)),
					$variableValueScope
				);
			}),

			'Type::Integer::minValue' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					$resolvedType->range->minValue === MinusInfinity::value ?
						new SubtypeValue(new TypeNameIdentifier("MinusInfinity"),
							new LiteralValue(new NullLiteral)
						) :
						new LiteralValue(new IntegerLiteral($resolvedType->range->minValue)),
					$variableValueScope
				);
			}),

			'Type::Integer::maxValue' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					$resolvedType->range->maxValue === PlusInfinity::value ?
						new SubtypeValue(new TypeNameIdentifier("PlusInfinity"),
							new LiteralValue(new NullLiteral)
						) :
						new LiteralValue(new IntegerLiteral($resolvedType->range->maxValue)),
					$variableValueScope
				);
			}),

			'Type::Real::minValue' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					$resolvedType->range->minValue === MinusInfinity::value ?
						new SubtypeValue(new TypeNameIdentifier("MinusInfinity"),
							new LiteralValue(new NullLiteral)
						) :
						new LiteralValue(new RealLiteral($resolvedType->range->minValue)),
					$variableValueScope
				);
			}),

			'Type::Real::maxValue' => new CallableNativeCodeHandler(function(
				TypeValue $target,
				Value $parameter,
				VariableValueScope $variableValueScope
			): ExecutionResultValueContext {
				$resolvedType = $this->resolvedType($target->type);
				return new ExecutionResultValueContext(
					$resolvedType->range->maxValue === PlusInfinity::value ?
						new SubtypeValue(new TypeNameIdentifier("PlusInfinity"),
							new LiteralValue(new NullLiteral)
						) :
						new LiteralValue(new RealLiteral($resolvedType->range->maxValue)),
					$variableValueScope
				);
			}),

			'Record::with' => new CallableNativeCodeHandler(function(
				DictValue          $target,
				DictValue          $parameter,
				VariableValueScope $variableValueScope,
			): ExecutionResultValueContext {
				return new ExecutionResultValueContext(
					new DictValue([... $target->items, ... $parameter->items]),
					$variableValueScope
				);
			}),

			'Subtype::baseValue' => new CallableNativeCodeHandler(function(
				SubtypeValue          $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext {
				return new ExecutionResultValueContext($target->baseValue, $variableValueScope);
			}),

			'Subtype::with' => new CallableNativeCodeHandler(function(
				SubtypeValue          $target,
				DictValue             $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext {
				/** @var DictValue $dict */
				$dict = $target->baseValue;
				$dictValue = new DictValue([... $dict->items, ... $parameter->items]);
				$subtype = $this->typeRef($target->typeName);
				if ($subtype instanceof SubtypeType) {
					$value = $this->functionExecutor->executeConstructor(
						$subExpressionExecutor,
						$subtype,
						$dictValue
					);
					return new ExecutionResultValueContext($value, $variableValueScope);
				}
				throw new ExecutorException(
					sprintf("Illegal attempt to apply ->with on %s",$target)
				);
			}),
			'Subtype::invoke' => new CallableNativeCodeHandler(function(
				SubtypeValue          $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext {
				$castValue = NoCastAvailable::value;
				$fromType = $target->typeName;
				$toType = new TypeNameIdentifier('Invokable');
				foreach($this->typeAssistant->allNamedTypes($this->typeAssistant->typeByName($fromType)) as $candidateFromType) {
					$castValue = $this->castRegistry->getCast($candidateFromType, $toType);
					if ($castValue !== NoCastAvailable::value) {
						break;
					}
				}
				if ($castValue === NoCastAvailable::value) {
					throw new ExecutorException(sprintf(
						"Unable to cast from %s to %s",
						$fromType, $toType
					));
				}
				$function = $this->functionExecutor->executeCastFunction(
					$subExpressionExecutor,
					$castValue,
					$target
				);
				if ($function instanceof ClosureValue || $function instanceof FunctionValue) {
					$value = $this->functionExecutor->executeFunction(
						$subExpressionExecutor,
						$function,
						$parameter
					);
					return new ExecutionResultValueContext(
						$value,
						$variableValueScope
					);
				}
				throw new ExecutorException(
					sprintf("Illegal attempt to use %s as a function",$parameter)
				);
			}),
			'Subtype::asText' => new CallableNativeCodeHandler(function(
				SubtypeValue          $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext {
				$castValue = NoCastAvailable::value;
				$fromType = $target->typeName;
				$toType = new TypeNameIdentifier('String');
				foreach($this->typeAssistant->allNamedTypes($this->typeAssistant->typeByName($fromType)) as $candidateFromType) {
					$castValue = $this->castRegistry->getCast($candidateFromType, $toType);
					if ($castValue !== NoCastAvailable::value) {
						break;
					}
				}
				if ($castValue === NoCastAvailable::value) {
					throw new ExecutorException(sprintf(
						"Unable to cast from %s to %s",
						$fromType, $toType
					));
				}
				$value = $this->functionExecutor->executeCastFunction(
					$subExpressionExecutor,
					$castValue,
					$target
				);
				return new ExecutionResultValueContext(
					$value,
					$variableValueScope
				);
			}),
			'Alias::asText' => new CallableNativeCodeHandler(function(
				Value                 $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor,
				NativeCodeExpression  $expression
			): ExecutionResultValueContext {
				$castValue = NoCastAvailable::value;
				$fromType = $expression->parameterType->typeName();
				$toType = new TypeNameIdentifier('String');
				foreach($this->typeAssistant->allNamedTypes($this->typeAssistant->typeByName($fromType)) as $candidateFromType) {
					$castValue = $this->castRegistry->getCast($candidateFromType, $toType);
					if ($castValue !== NoCastAvailable::value) {
						break;
					}
				}
				if ($castValue === NoCastAvailable::value) {
					throw new ExecutorException(sprintf(
						"Unable to cast from %s to %s",
						$fromType, $toType
					));
				}
				$value = $this->functionExecutor->executeCastFunction(
					$subExpressionExecutor,
					$castValue,
					$target
				);
				return new ExecutionResultValueContext(
					$value,
					$variableValueScope
				);
			}),
			'Type::asText' => new CallableNativeCodeHandler(function(
				TypeValue          $target,
				LiteralValue       $parameter,
				VariableValueScope $variableValueScope,
			): ExecutionResultValueContext {
				return new ExecutionResultValueContext(
					new LiteralValue(new StringLiteral((string)$target)),
					$variableValueScope
				);
			}),
			'Null::asText' => $this->buildNativeMethod(
				fn(null $target): string => 'null'
			),
			'Boolean::asText' => $this->buildNativeMethod(
				fn(bool $target): string => $target ? 'true' : 'false'
			),
			'String::asText' => $this->buildNativeMethod(
				fn(string $target): string => $target
			),
			'Real::asText' => $this->buildNativeMethod(
				fn(float $target): string => (string)$target
			),
			'Integer::asText' => $this->buildNativeMethod(
				fn(int $target): string => (string)$target
			),

			'Any::asBoolean' => new CallableNativeCodeHandler(function(
				Value                 $target,
				Value                 $parameter,
				VariableValueScope    $variableValueScope,
				SubExpressionExecutor $subExpressionExecutor
			): ExecutionResultValueContext {
				$castValue = NoCastAvailable::value;
				if ($target instanceof SubtypeValue) {
					$fromType = $target->typeName;
					$toType = new TypeNameIdentifier('Boolean');
					foreach($this->typeAssistant->allNamedTypes($this->typeAssistant->typeByName($fromType)) as $candidateFromType) {
						$castValue = $this->castRegistry->getCast($candidateFromType, $toType);
						if ($castValue !== NoCastAvailable::value) {
							break;
						}
					}
				}
				if ($castValue === NoCastAvailable::value) {
					return new ExecutionResultValueContext(
						new LiteralValue(new BooleanLiteral(true)),
						$variableValueScope
					);
				}
				$value = $this->functionExecutor->executeCastFunction(
					$subExpressionExecutor,
					$castValue,
					$target
				);
				return new ExecutionResultValueContext(
					$value,
					$variableValueScope
				);
			}),
			'Null::asBoolean' => $this->buildNativeMethod(
				fn(null $target): false => false
			),
			'Boolean::asBoolean' => $this->buildNativeMethod(
				fn(bool $target): bool => $target
			),
			'String::asBoolean' => $this->buildNativeMethod(
				fn(string $target): bool => $target !== ''
			),
			'Real::asBoolean' => $this->buildNativeMethod(
				fn(float $target): bool => $target !== 0.0
			),
			'Integer::asBoolean' => $this->buildNativeMethod(
				fn(int $target): bool => $target !== 0
			),

			'String::asIntegerNumber' => $this->buildNativeMethod(
				fn(string $target): int =>
					(string)($result = (int)$target) === $target ? $result :
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("StringIsNoIntegerNumber"),
							new LiteralValue(new StringLiteral($target))
						))
			),
			'String::asRealNumber' => $this->buildNativeMethod(
				fn(string $target): float =>
					(string)($result = (float)$target) === $target ? $result :
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("StringIsNoRealNumber"),
							new LiteralValue(new StringLiteral($target))
						))
			),

			'Enum::item' => new CallableNativeCodeHandler(function(
				TypeValue          $target,
				LiteralValue       $parameter,
				VariableValueScope $variableValueScope,
			): ExecutionResultValueContext {
				/** @var EnumerationValueType|EnumerationType $refType */
				$refType = $target->type;
				/** @var EnumerationType $enum */
				$enum = $refType instanceof EnumerationValueType ? $refType->enumType : $refType;
				foreach($enum->values as $value) {
					if ($value->identifier === $parameter->literal->value) {
						return new ExecutionResultValueContext(
							new EnumerationValue(
								$enum->typeName,
								$value
							),
							$variableValueScope
						);
					}
				}
				throw new ThrowResult(new SubtypeValue(
					new TypeNameIdentifier("EnumItemNotFound"),
					$parameter)
				);
			}),
			'Enum::textValue' => new CallableNativeCodeHandler(function(
				EnumerationValue   $target,
				LiteralValue       $parameter,
				VariableValueScope $variableValueScope,
			): ExecutionResultValueContext {
				return new ExecutionResultValueContext(
					new LiteralValue(new StringLiteral($target->enumValue->identifier)),
					$variableValueScope
				);
			}),
			'Enum::values' => new CallableNativeCodeHandler(function(
				EnumerationValue   $target,
				LiteralValue       $parameter,
				VariableValueScope $variableValueScope,
			): ExecutionResultValueContext {
				return new ExecutionResultValueContext(
					new ListValue(...
						array_map(
							fn(EnumValueIdentifier $value): EnumerationValue => new EnumerationValue(
								$target->enumTypeName,
								$value
							),
							$this->typeAssistant->typeByName($target->enumTypeName)->values
						)
					),
					$variableValueScope
				);
			}),
			'DatabaseConnection::query' => $this->buildNativeMethod(
				function(mixed $target, array $parameter): array {
					try {
						$pdo = new PDO($target->baseValue['dsn'], 'root', 'root');
						$stmt = $pdo->prepare($parameter['query']);
						$stmt->execute($parameter['boundParameters']);
						return $stmt->fetchAll(PDO::FETCH_ASSOC);
					} catch (PDOException $ex) {
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("QueryFailure"),
							$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast([
								'query' => $parameter['query'],
								'boundParameters' => $parameter['boundParameters'],
								'error' => $ex->getMessage(),
							])
						));
					}
				}
			),
			'DatabaseConnection::queryGrouped' => $this->buildNativeMethod(
				function(mixed $target, array $parameter): array {
					try {
						$pdo = new PDO($target->baseValue['dsn'], 'root', 'root');
						$stmt = $pdo->prepare($parameter['query']);
						$stmt->execute($parameter['boundParameters']);
						return $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
					} catch (PDOException $ex) {
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("QueryFailure"),
							$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast([
								'query' => $parameter['query'],
								'boundParameters' => $parameter['boundParameters'],
								'error' => $ex->getMessage(),
							])
						));
					}
				}
			),
			'DatabaseConnection::execute' => $this->buildNativeMethod(
				function(mixed $target, array $parameter): int {
					try {
						$pdo = new PDO($target->baseValue['dsn'], 'root', 'root');
						$stmt = $pdo->prepare($parameter['query']);
						$stmt->execute($parameter['boundParameters']);
						return $stmt->rowCount();
					} catch (PDOException $ex) {
						throw new ThrowResult(new SubtypeValue(
							new TypeNameIdentifier("QueryFailure"),
							$this->phpToCastWithSubtypesValueTransformer->fromPhpToCast([
								'query' => $parameter['query'],
								'boundParameters' => $parameter['boundParameters'],
								'error' => $ex->getMessage(),
							])
						));
					}
				}
			)
		];
	}

}