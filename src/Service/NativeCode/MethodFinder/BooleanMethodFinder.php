<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\NoMethodAvailable;

/**
 * @template-implements ByTypeMethodFinder<BooleanType>
 */
final readonly class BooleanMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	/**
	 * @param Type $originalTargetType
	 * @param BooleanType $targetType
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
		if ($methodName->identifier === 'unaryLogicNot') {
			return $this->getFunction("Boolean::unaryLogicNot",
				match($originalTargetType::class) {
					TrueType::class => new FalseType,
					FalseType::class => new TrueType,
					BooleanType::class => $originalTargetType
				});
		}
		if ($parameterType instanceof BooleanType ||
			$parameterType instanceof FalseType ||
			$parameterType instanceof TrueType
		) {
			if ($methodName->identifier === 'binaryLogicOr') {
				return $this->getFunction("Boolean::binaryLogicOr",
					$parameterType instanceof TrueType ||
					$originalTargetType instanceof TrueType ?
						new TrueType : new BooleanType);
			}
			if ($methodName->identifier === 'binaryLogicAnd') {
				return $this->getFunction("Boolean::binaryLogicAnd",
					$parameterType instanceof FalseType ||
					$originalTargetType instanceof FalseType ?
						new FalseType : new BooleanType);
			}
		}

		return NoMethodAvailable::value;
	}
}