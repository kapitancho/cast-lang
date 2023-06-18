<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariablePair;
use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Value\ClosureValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\ValueTypeResolver;

/**
 * @implements ExpressionAnalyser<ConstantExpression>
 * @implements ExpressionExecutor<ConstantExpression>
 */
final readonly class ConstantExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private SubtypeRelationChecker $subtypeRelationChecker,
		private ValueTypeResolver $valueTypeResolver,
	) {}

	/**
	 * @param ConstantExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser
	): ExecutionResultContext {
		$constant = $expression->constant;
		if ($constant instanceof FunctionValue) {
			$subExpressionResult = $subExpressionAnalyser->analyse(
				$constant->functionBody,
				$variableScope->withAddedVariablePairs(
					new VariablePair(
						new VariableNameIdentifier('#'),
						$constant->parameterType
					)
				)
			);
			if (!$this->subtypeRelationChecker->isSubtype(
				$subExpressionResult->expressionType,
				$constant->returnType->returnType
			)) {
				throw new AnalyserException(
					sprintf("Function %s is expected to return type %s, type %s returned instead",
						$constant,
						$constant->returnType->returnType,
						$subExpressionResult->expressionType,
					)
				);
			}
		}
		$type = $this->valueTypeResolver->findTypeOf($expression->constant);
		return new ExecutionResultContext(
			$type,
			new NothingType,
			new ErrorType,
			$variableScope
		);
	}

	/**
	 * @param ConstantExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$value = $expression->constant;
		if ($value instanceof FunctionValue) {
			$value = new ClosureValue($variableValueScope, $value);
		}
		return new ExecutionResultValueContext(
			$value,
			$variableValueScope
		);
	}
}