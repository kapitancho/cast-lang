<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\TypedValue;
use Cast\Model\Context\VariablePair;
use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValuePair;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\VariableAssignmentExpression;
use Cast\Model\Runtime\Type\FunctionType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\AnalyserExpressionSource;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;

/**
 * @implements ExpressionAnalyser<VariableAssignmentExpression>
 * @implements ExpressionExecutor<VariableAssignmentExpression>
 */
final readonly class VariableAssignmentExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {

	public function __construct(
		private AnalyserExpressionSource $analyserExpressionSource
	) {}

	/**
	 * @param VariableAssignmentExpression $expression
     * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		if (
			$expression->value instanceof ConstantExpression &&
			($fn = $expression->value->constant) instanceof FunctionValue
		) {
			$variableScope = $variableScope->withAddedVariablePairs(
				new VariablePair(
					$expression->variableName,
					new FunctionType(
						$fn->parameterType,
						$fn->returnType
					)
				)
			);
		}
		$ret = $subExpressionAnalyser->analyse($expression->value, $variableScope);
		return $ret->withVariableScope(
			$ret->variableScope->withAddedVariablePairs(
				new VariablePair(
					$expression->variableName,
					$ret->expressionType
				)
			)
		);
	}

	/**
	 * @param VariableAssignmentExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$ret = $subExpressionExecutor->execute($expression->value, $variableValueScope);
		return $ret->withGlobalScope(
			$ret->globalScope->withAddedValues(
				new VariableValuePair(
					$expression->variableName,
					new TypedValue(
						$this->analyserExpressionSource
							->getExpressionResult($expression->value)->expressionType,
						$ret->value,
					)
				)
			)
		);
	}
}