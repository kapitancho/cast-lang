<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Context\UnknownVariable;
use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\VariableNameExpression;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;

/**
 * @implements ExpressionAnalyser<VariableNameExpression>
 * @implements ExpressionExecutor<VariableNameExpression>
 */
final readonly class VariableNameExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private VariableValueScope $globalScope
	) {}

	/**
	 * @param VariableNameExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		$type = $variableScope->typeOf($expression->variableName);
		if ($type instanceof NothingType) {
			$type = $this->globalScope->typeOf($expression->variableName);
		}
		if ($type instanceof NothingType) {
			throw new AnalyserException(
				sprintf("Variable %s does not exist", $expression->variableName)
			);
		}
		return new ExecutionResultContext(
			$type,
			new NothingType(),
			new ErrorType,
			$variableScope
		);
	}

	/**
	 * @param VariableNameExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$value = $variableValueScope->findValueOf($expression->variableName);
		if ($value instanceof UnknownVariable) {
			$value = $this->globalScope->valueOf($expression->variableName);
		}
		return new ExecutionResultValueContext(
			$value,
			$variableValueScope
		);
	}
}