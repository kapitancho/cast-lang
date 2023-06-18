<?php

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\NoMethodAvailable;

/** @template T of Type */
interface ByTypeMethodFinder {
	/**
	 * @param Type $originalTargetType
	 * @param T $targetType
	 * @param PropertyNameIdentifier $methodName
	 * @param Type $parameterType
	 * @return FunctionValue|NoMethodAvailable
	 */
	public function findMethodFor(
		Type                   $originalTargetType,
		Type                   $targetType,
		PropertyNameIdentifier $methodName,
		Type                   $parameterType
	): FunctionValue|NoMethodAvailable;
}