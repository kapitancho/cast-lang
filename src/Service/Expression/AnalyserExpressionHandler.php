<?php

namespace Cast\Service\Expression;

use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Term\Term;
use Cast\Service\Execution\ExecutionResultContext;
use SplObjectStorage;

final class AnalyserExpressionHandler implements AnalyserExpressionSource, AnalyserExpressionStorage {

	private SplObjectStorage $storage;

	public function __construct() {
		$this->storage = new SplObjectStorage;
	}

	/**
	 * @param Term $expression
	 * @return ExecutionResultContext
	 * @throws AnalyserException
	 */
	public function getExpressionResult(Expression $expression): ExecutionResultContext {
		return $this->storage[$expression] ?? throw new AnalyserException(
			"Unknown expression $expression"
		);
	}

	public function storeExpressionResult(Expression $expression, ExecutionResultContext $executionResultContext): void {
		$this->storage[$expression] = $executionResultContext;
	}
}