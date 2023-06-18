<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Registry\NoCastAvailable;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\ExpressionToTypeConverter;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;

/**
 * @template-implements ByTypeMethodFinder<AnyType>
 */
final readonly class AnyMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	public function __construct(
		private TypeAssistant $typeAssistant,
		private SubtypeRelationChecker    $subtypeRelationChecker,
		private CastRegistry              $castRegistry,
		private ExpressionToTypeConverter $expressionToTypeConverter,
	) {}

	private function typeRef(string $name): Type {
		return $this->typeAssistant->typeByName(new TypeNameIdentifier($name));
	}

	/**
	 * @param Type $originalTargetType
	 * @param AnyType $targetType
	 * @param PropertyNameIdentifier $methodName
	 * @param Type $parameterType
	 * @return FunctionValue|NoMethodAvailable
	 */
	public function findMethodFor(
		Type                   $originalTargetType,
		Type                   $targetType,
		PropertyNameIdentifier $methodName,
		Type                   $parameterType
	): FunctionValue|NoMethodAvailable {
		if ($methodName->identifier === 'DEBUG') {
			return $this->getFunction("DEBUG", new AnyType);
		}

		if ($methodName->identifier === 'binaryEqual') {
			return $this->getFunction("Any::binaryEqual", new BooleanType);
		}
		if ($methodName->identifier === 'binaryNotEqual') {
			return $this->getFunction("Any::binaryNotEqual", new BooleanType);
		}

		if ($methodName->identifier === 'binarySubtype') {
			//if ($parameterType instanceof TypeExpression) {
				$realType = $this->expressionToTypeConverter->tryConvertExpressionToType(
					$parameterType
				);
				if (1||$realType) {
					return $this->getFunction("Any::isSubtypeOf", new BooleanType);
				}
			//}
		}

		if ($methodName->identifier === 'type') {
			return $this->getFunction("Any::type",
				new TypeType($originalTargetType));
		}
		if ($methodName->identifier === 'compilationType') {
			return $this->getFunction("Any::compilationType",
				new TypeType($originalTargetType));
		}
		if ($methodName->identifier === 'as') {
			if ($parameterType instanceof TypeType) {
				if ($this->subtypeRelationChecker->isSubtype($originalTargetType, $parameterType->refType)) {
					return $this->getFunction("Any::as", $parameterType->refType);
				}
				$fromType = $originalTargetType;
				$toType = $this->typeAssistant->closestTypeName($parameterType->refType);

				foreach($this->typeAssistant->allNamedTypes($fromType) as $candidateFromType) {
					if ($this->castRegistry->getCast($candidateFromType, $toType) !== NoCastAvailable::value) {
						return $this->getFunction("Any::as", $parameterType->refType);
					}
				}
				//if ($originalTargetType instanceof AnyType) {
					return $this->getFunction("Any::as",
						$parameterType->refType,
						new ErrorType(errorType: $this->typeRef('NoCastAvailable'))
					);
				//}
			}
		}
		if ($methodName->identifier === 'hydrateAs') {
			if ($parameterType instanceof TypeType) {
				if ($this->subtypeRelationChecker->isSubtype(
					$originalTargetType, $this->typeRef('JsonValue'))
				) {
					return $this->getFunction("JsonValue::hydrateAs",
						$parameterType->refType,
						new ErrorType(errorType: $this->typeRef('HydrationFailed'))
					);
				}
			}
		}
		if ($methodName->identifier === 'jsonValueToString') {
			if ($this->subtypeRelationChecker->isSubtype(
				$originalTargetType, $this->typeRef('JsonValue'))
			) {
				return $this->getFunction("JsonValue::jsonValueToString",
					StringType::base());
			}
		}

		if ($methodName->identifier === 'asJsonValue') {
			return $this->getFunction("Any::asJsonValue",
				$this->typeRef('JsonValue')
			);
		}
		if ($methodName->identifier === 'asJsonString') {
			return $this->getFunction("Any::asJsonString",
				StringType::base()
			);
		}
		if ($methodName->identifier === 'asDatabaseValue') {
			return $this->getFunction("Any::asDatabaseValue",
				$this->typeRef('DatabaseValue')
			);
		}

		if ($methodName->identifier === 'asText') {
			$searchType = $this->typeAssistant->toBasicType($originalTargetType);
			if ($searchType instanceof IntegerType) {
				return $this->getFunction("Integer::asText", new StringType(
					new LengthRange(1,
						$searchType->range->maxValue === PlusInfinity::value ? 1000 :
							max(
								(int)ceil(log10(abs($searchType->range->maxValue))),
								(int)ceil(log10(abs($searchType->range->minValue))) +
									($searchType->range->minValue < 0 ? 1 : 0)
							),
					))
				);
			}
			if ($searchType instanceof RealType) {
				return $this->getFunction("Real::asText", new StringType(
					new LengthRange(1, 1000)
				));
			}
			if ($searchType instanceof StringType) {
				return $this->getFunction("String::asText", new StringType(
					$searchType->range
				));
			}
			if ($searchType instanceof BooleanType) {
				return $this->getFunction("Boolean::asText", new StringType(
					new LengthRange(4, 5)
				));
			}
			if ($searchType instanceof TrueType) {
				return $this->getFunction("Boolean::asText", new StringType(
					new LengthRange(4, 4)
				));
			}
			if ($searchType instanceof FalseType) {
				return $this->getFunction("Boolean::asText", new StringType(
					new LengthRange(5, 5)
				));
			}
			if ($searchType instanceof NullType) {
				return $this->getFunction("Null::asText", new StringType(
					new LengthRange(4, 4)
				));
			}
			if ($searchType instanceof TypeType) {
				return $this->getFunction("Type::asText", StringType::base());
			}
			if ($this->subtypeRelationChecker->isSubtype($targetType, new NullType)) {
				return $this->getFunction("Null::asText", new StringType(
					new LengthRange(4, 4)
				));
			}
		}

		if ($methodName->identifier === 'asBoolean') {
			$searchType = $this->typeAssistant->toBasicType($originalTargetType);
			if ($searchType instanceof IntegerType) {
				return $this->getFunction("Integer::asBoolean", new BooleanType);
			}
			if ($searchType instanceof RealType) {
				return $this->getFunction("Real::asBoolean", new BooleanType);
			}
			if ($searchType instanceof StringType) {
				return $this->getFunction("String::asBoolean", new BooleanType);
			}
			if ($searchType instanceof BooleanType || $searchType instanceof TrueType || $searchType instanceof FalseType) {
				return $this->getFunction("Boolean::asBoolean", new BooleanType);
			}
			if ($searchType instanceof NullType) {
				return $this->getFunction("Null::asBoolean", new FalseType);
			}
			return $this->getFunction("Any::asBoolean", new TrueType);
		}
		return NoMethodAvailable::value;
	}
}