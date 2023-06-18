<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Execution\Flow\FlowShortcut;

/**
 * @template E of Expression
 */
interface ExpressionExecutor {
	/**
	 * @param E $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 * @throws FlowShortcut
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor,
	): ExecutionResultValueContext;
}