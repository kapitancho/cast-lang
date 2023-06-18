<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Service\Execution\ExecutionResultContext;

/**
 * @implements SubExpressionAnalyser
 */
final readonly class ContextCodeExpressionAnalyser implements SubExpressionAnalyser {

	public function __construct(
		private CodeExpressionAnalyser $codeExpressionAnalyser,
		private AnalyserExpressionStorage $analyserExpressionStorage,
	) {}

	public function analyse(Expression $expression, VariableScope $variableScope): ExecutionResultContext {
		//TODO - user globalVariableScope
		$result = $this->codeExpressionAnalyser->analyse(
			$expression,
			$variableScope,
			$this
		);
		$this->analyserExpressionStorage->storeExpressionResult(
			$expression, $result
		);
		return $result;
	}
}