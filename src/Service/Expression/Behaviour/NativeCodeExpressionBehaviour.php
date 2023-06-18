<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\NativeCodeExpression;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\NativeCode\NativeCodeBridge;

/**
 * @implements ExpressionExecutor<NativeCodeExpression>
 */
final readonly class NativeCodeExpressionBehaviour implements ExpressionExecutor {
	public function __construct(
		private NativeCodeBridge $nativeCodeBridge,
	) {}

	/**
	 * @param NativeCodeExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		return $this->nativeCodeBridge->getById($expression->id)->execute(
			$variableValueScope->valueOf(new VariableNameIdentifier('$')),
			$variableValueScope->valueOf(new VariableNameIdentifier('#')),
			$variableValueScope,
			$subExpressionExecutor,
			$expression,
			$variableValueScope->typeOf(new VariableNameIdentifier('$')),
			$variableValueScope->typeOf(new VariableNameIdentifier('#')),
		);
	}
}