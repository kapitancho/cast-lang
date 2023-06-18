<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Term\Term;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Execution\Flow\FlowShortcut;

final readonly class ContextCodeExpressionExecutor implements SubExpressionExecutor {

	public function __construct(
		private CodeExpressionExecutor $codeExpressionExecutor,
	) {}

	/**
	 * @param Term $expression
	 * @param VariableValueScope $variableValueScope
	 * @return ExecutionResultValueContext
	 * @throws FlowShortcut
	 */
	public function execute(
		Expression $expression,
		VariableValueScope $variableValueScope
	): ExecutionResultValueContext {
		return $this->codeExpressionExecutor->execute(
			$expression,
			$variableValueScope,
			$this
		);
	}
}