<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Expression\SubExpressionAnalyser;

final readonly class NestedMethodFinder implements MethodFinder {
	/** @var MethodFinder[] */
	private array $methodFinders;
	public function __construct(
		MethodFinder ... $methodFinders
	) {
		$this->methodFinders = $methodFinders;
	}

	public function findMethodFor(
		Type                   $targetType,
		PropertyNameIdentifier $methodName,
		Type                   $parameterType,
		SubExpressionAnalyser|null  $subExpressionAnalyser
	): FunctionValue|NoMethodAvailable {
		foreach($this->methodFinders as $methodFinder) {
			if (($fn = $methodFinder->findMethodFor(
				$targetType, $methodName, $parameterType, $subExpressionAnalyser
			)) !== NoMethodAvailable::value) {
				return $fn;
			}
		}
		return NoMethodAvailable::value;
	}
}