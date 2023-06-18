<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\TypeAssistant;

/**
 * @template-implements ByTypeMethodFinder<RealType>
 */
final readonly class RealMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	public function __construct(private TypeAssistant $typeAssistant) {}

	/**
	 * @param Type $originalTargetType
	 * @param RealType $targetType
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
		if ($parameterType instanceof IntegerType || $parameterType instanceof RealType) {
			if (in_array($methodName->identifier, [
				'binaryLessThan', 'binaryLessThanEqual',
				'binaryGreaterThan', 'binaryGreaterThanEqual',
			], true)) {
				return $this->getFunction("Numeric::" . $methodName->identifier,
					new BooleanType);
			}
		}
		if ($methodName->identifier === 'unaryMinus') {
			return $this->getFunction("Real::unaryMinus",
				new RealType(
					new RealRange(
						$targetType->range->maxValue === PlusInfinity::value ?
							MinusInfinity::value : -$targetType->range->maxValue,
						$targetType->range->minValue === MinusInfinity::value ?
							PlusInfinity::value : -$targetType->range->minValue
					)
				));
		}
		if ($methodName->identifier === 'unaryPlus') {
			return $this->getFunction("Real::unaryPlus",
				new RealType($targetType->range));
		}
		if ($methodName->identifier === 'asInteger') {
			return $this->getFunction("Real::asInteger",
				new IntegerType(
					new IntegerRange(
						$targetType->range->minValue === MinusInfinity::value ?
							MinusInfinity::value : (int)$targetType->range->minValue,
						$targetType->range->maxValue === PlusInfinity::value ?
							PlusInfinity::value : (int)$targetType->range->maxValue,
					)
				));
		}
		if ($methodName->identifier === 'roundAsInteger') {
			return $this->getFunction("Real::roundAsInteger",
				new IntegerType(
					new IntegerRange(
						$targetType->range->minValue === MinusInfinity::value ?
							MinusInfinity::value : round($targetType->range->minValue),
						$targetType->range->maxValue === PlusInfinity::value ?
							PlusInfinity::value : round($targetType->range->maxValue),
					)
				));
		}
		if ($methodName->identifier === 'roundAsDecimal') {
			if ($parameterType instanceof IntegerType && $parameterType->range->minValue >= 0) {
				return $this->getFunction("Real::roundAsDecimal",
					new RealType(
						new RealRange(
							$targetType->range->minValue === MinusInfinity::value ?
								MinusInfinity::value : floor($targetType->range->minValue),
							$targetType->range->maxValue === PlusInfinity::value ?
								PlusInfinity::value : ceil($targetType->range->maxValue),
						)
					));
			}
		}
		if ($methodName->identifier === 'floor') {
			return $this->getFunction("Real::floor",
				new IntegerType(
					new IntegerRange(
						$targetType->range->minValue === MinusInfinity::value ?
							MinusInfinity::value : floor($targetType->range->minValue),
						$targetType->range->maxValue === PlusInfinity::value ?
							PlusInfinity::value : floor($targetType->range->maxValue),
					)
				));
		}
		if ($methodName->identifier === 'ceil') {
			return $this->getFunction("Real::ceil",
				new IntegerType(
					new IntegerRange(
						$targetType->range->minValue === MinusInfinity::value ?
							MinusInfinity::value : ceil($targetType->range->minValue),
						$targetType->range->maxValue === PlusInfinity::value ?
							PlusInfinity::value : ceil($targetType->range->maxValue),
					)
				));
		}
		if ($methodName->identifier === 'binaryPlus') {
			if ($parameterType instanceof RealType || $parameterType instanceof IntegerType) {
				return $this->getFunction("Real::plus", new RealType(
					new RealRange(
						$targetType->range->minValue === MinusInfinity::value ||
						$parameterType->range->minValue === MinusInfinity::value ?
							MinusInfinity::value :
						$targetType->range->minValue + $parameterType->range->minValue,
						$targetType->range->maxValue === PlusInfinity::value ||
						$parameterType->range->maxValue === PlusInfinity::value ?
							PlusInfinity::value :
						$targetType->range->maxValue + $parameterType->range->maxValue,
					),
				));
			}
		}
		if ($methodName->identifier === 'binaryMinus') {
			if ($parameterType instanceof RealType || $parameterType instanceof IntegerType) {
				return $this->getFunction("Real::minus", new RealType(
					new RealRange(
						$targetType->range->minValue === MinusInfinity::value ||
						$parameterType->range->maxValue === PlusInfinity::value ?
							MinusInfinity::value :
						$targetType->range->minValue - $parameterType->range->maxValue,
						$targetType->range->maxValue === PlusInfinity::value ||
						$parameterType->range->minValue === MinusInfinity::value ?
							PlusInfinity::value :
						$targetType->range->maxValue - $parameterType->range->minValue,
					),
				));
			}
		}
		if ($methodName->identifier === 'binaryMultiplication') {
			if ($parameterType instanceof RealType || $parameterType instanceof IntegerType) {
				return $this->getFunction("Real::multiply", RealType::base());
			}
		}
		if ($methodName->identifier === 'binaryPower') {
			if ($parameterType instanceof RealType || $parameterType instanceof IntegerType) {
				return $this->getFunction("Real::power", RealType::base());
			}
		}
		if ($methodName->identifier === 'binaryDivision') {
			if ($parameterType instanceof RealType || $parameterType instanceof IntegerType) {
				return $this->getFunction("Real::divide", RealType::base(),
					new ErrorType(
						errorType: $this->typeAssistant->typeByName(
							new TypeNameIdentifier("DivisionByZero")),
					));
			}
		}
		if ($methodName->identifier === 'binaryModulo') {
			if ($parameterType instanceof RealType || $parameterType instanceof IntegerType) {
				return $this->getFunction("Real::modulo", RealType::base(),
					new ErrorType(
						errorType: $this->typeAssistant->typeByName(
							new TypeNameIdentifier("DivisionByZero")),
					));
			}
		}

		return NoMethodAvailable::value;
	}
}