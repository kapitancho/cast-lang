<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Service\Execution\ExecutionResultContext;

final readonly class CodeExpressionAnalyser {

	public function __construct(private ExpressionBehaviourCollection $behaviourCollection) {}

	public function analyse(Expression $expression, VariableScope $variableScope, SubExpressionAnalyser $subExpressionAnalyser): ExecutionResultContext {
		return $this->behaviourCollection->getAnalyser($expression)
			->analyse($expression, $variableScope, $subExpressionAnalyser);
	}
}