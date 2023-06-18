<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Service\Execution\ExecutionResultValueContext;

final readonly class CodeExpressionExecutor {

	public function __construct(private ExpressionBehaviourCollection $behaviourCollection) {}

	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		return $this->behaviourCollection->getExecutor($expression)->execute(
			$expression,
			$variableValueScope,
			$subExpressionExecutor
		);
	}
}