<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Expression\SubExpressionAnalyser;

interface MethodFinder {
	public function findMethodFor(
		Type                   $targetType,
		PropertyNameIdentifier $methodName,
		Type                   $parameterType,
		SubExpressionAnalyser|null  $subExpressionAnalyser
	): FunctionValue|NoMethodAvailable;
}