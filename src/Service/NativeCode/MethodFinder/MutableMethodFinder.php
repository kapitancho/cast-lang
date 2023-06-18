<?php

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Runtime\Type\MutableType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\SubtypeRelationChecker;

/**
 * @template-implements ByTypeMethodFinder<MutableType>
 */
final readonly class MutableMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	public function __construct(
		private SubtypeRelationChecker $subtypeRelationChecker
	) {}

	/**
	 * @param Type $originalTargetType
	 * @param MutableType $targetType
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

		if ($methodName->identifier === 'value') {
			return $this->getFunction("Mutable::value", $refType);
		}

		if ($methodName->identifier === 'SET') {
			if ($this->subtypeRelationChecker->isSubtype($parameterType, $refType)) {
				return $this->getFunction("Mutable::SET",$refType);
			}
		}

		return NoMethodAvailable::value;
	}
}