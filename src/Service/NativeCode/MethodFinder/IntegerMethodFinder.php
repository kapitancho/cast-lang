<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\TypeAssistant;

/**
 * @template-implements ByTypeMethodFinder<IntegerType>
 */
final readonly class IntegerMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	public function __construct(private TypeAssistant $typeAssistant) {}

	/**
	 * @param Type $originalTargetType
	 * @param IntegerType $targetType
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
		if ($parameterType instanceof IntegerType) {
			if ($methodName->identifier === 'upTo') {
				$maxLength = max(0,
					$parameterType->range->maxValue === PlusInfinity::value ||
					$targetType->range->minValue === MinusInfinity::value ? PlusInfinity::value :
						1 + $parameterType->range->maxValue - $targetType->range->minValue,
				);
				return $this->getFunction("Integer::upTo",
					new ArrayType(
						$maxLength > 0 ? new IntegerType(
							new IntegerRange(
								$targetType->range->minValue,
								$parameterType->range->maxValue
							)
						) : new NothingType,
						new LengthRange(
							max(0,
								$targetType->range->maxValue === PlusInfinity::value ||
								$parameterType->range->minValue === MinusInfinity::value ? 0 :
									1 + $parameterType->range->minValue - $targetType->range->maxValue
							),
							$maxLength
						)
					)
				);
			}
			if ($methodName->identifier === 'downTo') {
				$maxLength = max(0,
					$targetType->range->maxValue === PlusInfinity::value ||
					$parameterType->range->minValue === MinusInfinity::value ? PlusInfinity::value :
						1 + $targetType->range->maxValue - $parameterType->range->minValue,
				);
				return $this->getFunction("Integer::downTo",
					new ArrayType(
						$maxLength > 0 ? new IntegerType(
							new IntegerRange(
								$parameterType->range->minValue,
								$targetType->range->maxValue,
							)
						) : new NothingType,
						new LengthRange(
							max(0,
								$parameterType->range->maxValue === PlusInfinity::value ||
								$targetType->range->minValue === MinusInfinity::value ? 0 :
									1 + $targetType->range->minValue - $parameterType->range->maxValue
							),
							$maxLength
						)
					)
				);
			}
		}
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
			return $this->getFunction("Integer::unaryMinus",
				new IntegerType(
					new IntegerRange(
						$targetType->range->maxValue === PlusInfinity::value ?
							MinusInfinity::value : -$targetType->range->maxValue,
						$targetType->range->minValue === MinusInfinity::value ?
							PlusInfinity::value : -$targetType->range->minValue
					)
				));
		}
		if ($methodName->identifier === 'unaryPlus') {
			return $this->getFunction("Integer::unaryPlus",
				new IntegerType($targetType->range));
		}
		if ($methodName->identifier === 'unaryBitwiseNot') {
			return $this->getFunction("Integer::unaryBitwiseNot",
				IntegerType::base());
		}
		if ($parameterType instanceof IntegerType) {
			if (in_array($methodName->identifier, [
				'binaryBitwiseOr', 'binaryBitwiseAnd',
				'binaryBitwiseXor'
			], true)) {
				return $this->getFunction("Integer::" . $methodName->identifier,
					IntegerType::base());
			}
		}

		if ($methodName->identifier === 'binaryModulo') {
			if ($parameterType instanceof IntegerType && $parameterType->range->minValue > 0) {
				return $this->getFunction("Integer::binaryModulo", $targetType);
			}
		}

		if ($methodName->identifier === 'binaryPlus') {
			if ($parameterType instanceof IntegerType) {
				return $this->getFunction("Integer::plus", new IntegerType(
					new IntegerRange(
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

			if ($parameterType instanceof RealType) {
				return $this->getFunction("Integer::plusReal", new RealType(
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
			if ($parameterType instanceof IntegerType) {
				return $this->getFunction("Integer::minus", new IntegerType(
					new IntegerRange(
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
			if ($parameterType instanceof RealType) {
				return $this->getFunction("Integer::minusReal", new RealType(
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
			if ($parameterType instanceof IntegerType) {
				return $this->getFunction("Integer::multiply", IntegerType::base());
			}
			if ($parameterType instanceof RealType) {
				return $this->getFunction("Integer::multiplyReal", RealType::base());
			}
		}
		if ($methodName->identifier === 'binaryPower') {
			if ($parameterType instanceof IntegerType) {
				return $this->getFunction("Integer::power", IntegerType::base());
			}
			if ($parameterType instanceof RealType) {
				return $this->getFunction("Integer::powerReal", RealType::base());
			}
		}
		if ($methodName->identifier === 'binaryDivision') {
			if ($parameterType instanceof IntegerType) {
				return $this->getFunction("Integer::divide", RealType::base(),
					new ErrorType(
						errorType: $this->typeAssistant->typeByName(
							new TypeNameIdentifier("DivisionByZero")),
					));
			}
			if ($parameterType instanceof RealType) {
				return $this->getFunction("Integer::divideReal", RealType::base(),
					new ErrorType(
						errorType: $this->typeAssistant->typeByName(
							new TypeNameIdentifier("DivisionByZero")),
					));
			}
		}
		if ($methodName->identifier === 'binaryIntegerDivision') {
			if ($parameterType instanceof IntegerType) {
				return $this->getFunction("Integer::integerDivide", IntegerType::base(),
					new ErrorType(
						errorType: $this->typeAssistant->typeByName(
							new TypeNameIdentifier("DivisionByZero")),
					));
			}
		}
		if ($methodName->identifier === 'binaryModulo') {
			if ($parameterType instanceof IntegerType) {
				return $this->getFunction("Integer::modulo", IntegerType::base(),
					new ErrorType(
						errorType: $this->typeAssistant->typeByName(
							new TypeNameIdentifier("DivisionByZero")),
					));
			}
			if ($parameterType instanceof RealType) {
				return $this->getFunction("Integer::moduloReal", RealType::base(),
					new ErrorType(
						errorType: $this->typeAssistant->typeByName(
							new TypeNameIdentifier("DivisionByZero")),
					));
			}
		}

		return NoMethodAvailable::value;
	}
}