<?php

namespace Cast\Service\Expression;

use Cast\Model\Program\Expression\Expression;
use Cast\Service\Execution\ExecutionResultContext;

interface AnalyserExpressionSource {
	/**
	 * @param Expression $expression
	 * @return ExecutionResultContext
	 * @throws AnalyserException
	 */
	public function getExpressionResult(Expression $expression): ExecutionResultContext;
}