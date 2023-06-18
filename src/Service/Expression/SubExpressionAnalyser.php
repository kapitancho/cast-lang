<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Service\Execution\ExecutionResultContext;

interface SubExpressionAnalyser {
	public function analyse(
		Expression    $expression,
		VariableScope $variableScope
	): ExecutionResultContext;
}