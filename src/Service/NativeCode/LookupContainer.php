<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\NamedType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\TypeCast;
use Cast\Model\Runtime\Value\ClosureValue;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\ValueTypeResolver;

final class LookupContainer implements Container {

	private readonly array $lookup;

	private array $cache = [];

	public function __construct(
		private readonly TypeAssistant $typeAssistant,
		private readonly SubtypeRelationChecker $subtypeRelationChecker,
		private readonly CastRegistry $castRegistry,
		private readonly FunctionExecutor $functionExecutor,
		private readonly ValueTypeResolver $valueTypeResolver,
		private readonly ListValue $configuration,
		private readonly SubExpressionExecutor $subExpressionExecutor,
		private readonly Value $containerValue
	) {
		$lookup = [];
		foreach($this->configuration->items as $configurationItem) {
			if ($configurationItem instanceof ListValue) {
				$type = $configurationItem->items[0]->type;
				$rule = $configurationItem->items[1];
			} elseif ($configurationItem instanceof TypeValue) {
				$type = $configurationItem->type->types[0];
				$rule = $configurationItem->type->types[1];
			} else {
				$type = null;
				$rule = null;
			}
			if ($rule && $type instanceof NamedType) {
				$lookup[$type->typeName()->identifier] = $rule;
			}
		}
		$this->lookup = $lookup;
	}

	/** @throws ContainerException */
	public function lookup(Type $targetType, NamedType $lookupType): Value {
		$typeName = $lookupType->typeName();
		$lookup = $this->lookup[$typeName->identifier] ?? null;
		if ($lookup instanceof FunctionValue || $lookup instanceof ClosureValue) {
			$lookupFn = $lookup instanceof ClosureValue ? $lookup->function : $lookup;
			if ($this->subtypeRelationChecker->isSubtype(
				$lookupFn->returnType->returnType, $targetType
			)) {
				$parameterType = $this->typeAssistant->followAliases($lookupFn->parameterType);
				if ($parameterType instanceof NullType) {
					$par = new LiteralValue(new NullLiteral);
				} elseif ($parameterType instanceof RecordType) {
					$args = [];
					foreach($parameterType->types as $key => $type) {
						$args[$key] = $this->instanceOf($type);
					}
					$par = new DictValue($args);
				}
				return $this->functionExecutor->executeFunction(
					$this->subExpressionExecutor,
					$lookup,
					$par,
				);
			}
			throw new ContainerException(
				$targetType,
				"The return type of the mapping function is incompatible"
			);
		}
		if ($lookup instanceof SubtypeType) {
			return $this->lookup($targetType, $lookup);
		}
		if ($lookup instanceof AliasType) {
			return $this->lookup($targetType, $lookup);
		}
		if ($lookupType instanceof SubtypeType) {
			$lookupBaseType = $lookupType->baseType();
			if ($lookupBaseType instanceof RecordType) {
				$values = [];
				$isDict = $lookup instanceof DictValue;
				foreach($lookupBaseType->types as $key => $type) {
					if ($isDict && array_key_exists($key, $lookup->items)) {
						$val = $lookup->items[$key];
						if ($this->subtypeRelationChecker->isSubtype(
							$this->valueTypeResolver->findTypeOf($val),
							$type
						)) {
							$values[$key] = $val;
						} else {
							throw new ContainerException(
								$targetType,
								sprintf("The resolved value for %s has an incorrect type",
									$key)
							);
						}
					} else {
						$values[$key] = $this->instanceOf($type);
					}
				}

				$result = new SubtypeValue(
					$lookupType->typeName(),
					new DictValue($values)
				);
				return $result;
			}
			if ($lookupBaseType instanceof TupleType) {
				$values = [];
				$isList = $lookup instanceof ListValue;
				foreach($lookupBaseType->types as $key => $type) {
					if ($isList && array_key_exists($key, $lookup->items)) {
						$val = $lookup->items[$key];
						if ($this->subtypeRelationChecker->isSubtype(
							$this->valueTypeResolver->findTypeOf($val),
							$type
						)) {
							$values[$key] = $val;
						} else {
							throw new ContainerException(
								$targetType,
								sprintf("The resolved value for %s has an incorrect type",
									$key)
							);
						}
					} else {
						$values[$key] = $this->instanceOf($type);
					}
				}
				return new SubtypeValue(
					$lookupType->typeName(),
					new ListValue(... $values)
				);
			}
		}

		$castsTo = $this->castRegistry->getAllCastsTo($lookupType->typeName());
		if (count($castsTo) === 1) {
			$cast = $castsTo[array_key_first($castsTo)];
			$param = $this->instanceOf($this->typeAssistant->typeByName($cast->castFromType));

			return $this->functionExecutor->executeCastFunction(
				$this->subExpressionExecutor,
				$cast,
				$param
			);
		}
		throw new ContainerException(
			$targetType,
			sprintf("No appropriate configuration found for type %s (# of casts: %s)",
				$lookupType->typeName(), count($castsTo))
		);
	}
	private int $counter = 0;

	private function resolveInstanceOf(Type&NamedType $targetType): Value {
		if ($targetType->typeName()->identifier === 'Container') {
			return $this->containerValue;
		}
		$result = $this->lookup($targetType, $targetType);
		if ($this->subtypeRelationChecker->isSubtype(
			$t = $this->valueTypeResolver->findTypeOf($result),
			$targetType
		)) {
			return $result;
		}
		if ($t instanceof NamedType) {
			$cast = $this->castRegistry->getCast(
				$t->typeName(),
				$targetType->typeName(),
			);
			if ($cast instanceof TypeCast) {
				return $this->functionExecutor->executeCastFunction(
					$this->subExpressionExecutor,
					$cast,
					$result
				);
			}
		}
		throw new ContainerException(
			$targetType,
			sprintf("The resolved value has an incorrect type")
		);
	}

	/** @throws ContainerException */
	public function instanceOf(Type $targetType): Value {
		if ($targetType instanceof NamedType) {
			$key = $targetType->typeName()->identifier;
			$val = $this->cache[$key] ?? null;
			if ($val === null) {
				$this->cache[$key] = true;
			}
			if ($val === true) {
				throw new ContainerException(
					$targetType,
					sprintf("Recursion detected when trying to build instance of %s",
						$targetType)
				);
			}
			if ($val) {
				return $val;
			}
			$result = $this->resolveInstanceOf($targetType);
			$this->cache[$key] = $result;
			return $result;
		}
		throw new ContainerException(
			$targetType,
			sprintf("Cannot create an instance of a non-named type %s",
				$targetType)
		);
	}

}