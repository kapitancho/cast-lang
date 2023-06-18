<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Execution\Flow\FlowShortcut;

interface SubExpressionExecutor {
	/**
	 * @param Expression $expression
	 * @param VariableValueScope $variableValueScope
	 * @return ExecutionResultValueContext
	 * @throws FlowShortcut
	 */
	public function execute(
		Expression         $expression,
		VariableValueScope $variableValueScope
	): ExecutionResultValueContext;
}