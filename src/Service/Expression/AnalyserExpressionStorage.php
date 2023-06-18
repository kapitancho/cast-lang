<?php

namespace Cast\Service\Expression;

use Cast\Model\Program\Expression\Expression;
use Cast\Service\Execution\ExecutionResultContext;

/**
 * @template E of Expression
 */
interface AnalyserExpressionStorage {
	public function storeExpressionResult(
		Expression $expression,
		ExecutionResultContext $executionResultContext
	): void;
}