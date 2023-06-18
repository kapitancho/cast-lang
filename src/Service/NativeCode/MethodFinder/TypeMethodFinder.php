<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\EnumerationValueType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\TypeAssistant;

/**
 * @template-implements ByTypeMethodFinder<TypeType>
 */
final readonly class TypeMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	public function __construct(
		private TypeAssistant $typeAssistant
	) {}

	private function typeRef(TypeNameIdentifier|string $name): Type {
		return $this->typeAssistant->typeByName($name instanceof TypeNameIdentifier ?
			$name : new TypeNameIdentifier($name)
		);
	}

	/**
	 * @param Type $originalTargetType
	 * @param TypeType $targetType
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
		$refType = $targetType->refType;
		while($refType instanceof AliasType) {
			$refType = $refType->aliasedType();
		}
		if ($methodName->identifier === 'refType') {
			if ($refType instanceof TypeType) {
				return $this->getFunction("Type::Type::refType",
					new TypeType($refType->refType)
				);
			}
		}
		if ($methodName->identifier === 'itemType') {
			if ($refType instanceof ArrayType) {
				return $this->getFunction("Type::Array::itemType",
					new TypeType($refType->itemType)
				);
			}
			if ($refType instanceof MapType) {
				return $this->getFunction("Type::Map::itemType",
					new TypeType($refType->itemType)
				);
			}
		}
		if ($methodName->identifier === 'minLength') {
			if ($refType instanceof StringType) {
				return $this->getFunction("Type::String::minLength",
					new IntegerType(new IntegerRange(0, PlusInfinity::value))
				);
			}
			if ($refType instanceof ArrayType) {
				return $this->getFunction("Type::Array::minLength",
					new IntegerType(new IntegerRange(0, PlusInfinity::value))
				);
			}
			if ($refType instanceof MapType) {
				return $this->getFunction("Type::Map::minLength",
					new IntegerType(new IntegerRange(0, PlusInfinity::value))
				);
			}
		}
		if ($methodName->identifier === 'maxLength') {
			if ($refType instanceof StringType) {
				return $this->getFunction("Type::String::maxLength",
					$refType->range->maxLength === PlusInfinity::value ?
						new UnionType(
							new IntegerType(new IntegerRange(0, PlusInfinity::value)),
							$this->typeRef("PlusInfinity")
						) : new IntegerType(new IntegerRange(0, $refType->range->maxLength)),

				);
			}
			if ($refType instanceof ArrayType) {
				return $this->getFunction("Type::Array::maxLength",
					$refType->range->maxLength === PlusInfinity::value ?
						new UnionType(
							new IntegerType(new IntegerRange(0, PlusInfinity::value)),
							$this->typeRef("PlusInfinity")
						) : new IntegerType(new IntegerRange(0, $refType->range->maxLength)),

				);
			}
			if ($refType instanceof MapType) {
				return $this->getFunction("Type::Map::maxLength",
					$refType->range->maxLength === PlusInfinity::value ?
						new UnionType(
							new IntegerType(new IntegerRange(0, PlusInfinity::value)),
							$this->typeRef("PlusInfinity")
						) : new IntegerType(new IntegerRange(0, $refType->range->maxLength)),

				);
			}
		}
		if ($methodName->identifier === 'minValue') {
			if ($refType instanceof IntegerType) {
				return $this->getFunction("Type::Integer::minValue",
					$refType->range->minValue === MinusInfinity::value ?
						new UnionType(
							IntegerType::base(),
							$this->typeRef("MinusInfinity")
						) : IntegerType::base()
				);
			}
			if ($refType instanceof RealType) {
				return $this->getFunction("Type::Real::minValue",
					$refType->range->minValue === MinusInfinity::value ?
						new UnionType(
							RealType::base(),
							$this->typeRef("MinusInfinity")
						) : RealType::base()
				);
			}
		}
		if ($methodName->identifier === 'maxValue') {
			if ($refType instanceof IntegerType) {
				return $this->getFunction("Type::Integer::maxValue",
					$refType->range->maxValue === PlusInfinity::value ?
						new UnionType(
							IntegerType::base(),
							$this->typeRef("PlusInfinity")
						) : IntegerType::base()
				);
			}
			if ($refType instanceof RealType) {
				return $this->getFunction("Type::Real::maxValue",
					$refType->range->maxValue === PlusInfinity::value ?
						new UnionType(
							RealType::base(),
							$this->typeRef("PlusInfinity")
						) : RealType::base()
				);
			}
		}
		if ($methodName->identifier === 'value') {
			if ($refType instanceof EnumerationValueType) {
				return new FunctionValue(
					new NullType,
					new FunctionReturnType($refType),
					new FunctionBodyExpression(
						new ConstantExpression(
							new EnumerationValue(
								$refType->enumType->typeName,
								$refType->enumValue,
							)
						)
					)
				);
			}
		}
		if ($methodName->identifier === 'values') {
			if ($refType instanceof EnumerationValueType) {
				$refType = $refType->enumType;
			}
			if ($refType instanceof EnumerationType) {
				/*$l = max(array_map(static fn(EnumValueIdentifier $value): int =>
					strlen($value->identifier), $refType->values) ?: [0]);*/
				$a = count($refType->values);
				return new FunctionValue(
					new NullType,
					new FunctionReturnType(
						new ArrayType(
							$this->typeRef($refType->typeName),
							new LengthRange($a, $a)
						)
					),
					new FunctionBodyExpression(
						new ConstantExpression(
							new ListValue(...
								array_map(
									static fn(EnumValueIdentifier $value): EnumerationValue =>
										new EnumerationValue(
											$refType->typeName,
											$value
										),
									$refType->values
								)
							)
						)
					)
				);
			}
		}
		if ($methodName->identifier === 'valueWithName') {
			if ($refType instanceof EnumerationValueType) {
				$refType = $refType->enumType;
			}
			if ($refType instanceof EnumerationType) {
				return $this->getFunction("Enum::item",
					$this->typeRef($refType->typeName),
					new ErrorType(
						false,$this->typeRef("EnumItemNotFound")
					)
				);
			}
		}
		return NoMethodAvailable::value;
	}
}