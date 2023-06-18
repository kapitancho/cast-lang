<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Service\Execution\ExecutionResultContext;

/**
 * @template E of Expression
 */
interface ExpressionAnalyser {
	/**
	 * @param E $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression            $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext;
}