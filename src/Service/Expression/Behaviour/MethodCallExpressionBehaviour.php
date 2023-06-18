<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\MethodCallExpression;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\AnalyserExpressionSource;
use Cast\Service\Expression\ExecutorException;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Registry\MethodFinder;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @implements ExpressionAnalyser<MethodCallExpression>
 * @implements ExpressionExecutor<MethodCallExpression>
 */
final readonly class MethodCallExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private FunctionExecutor $functionExecutor,
		private MethodFinder $methodFinder,
		private UnionTypeNormalizer $unionTypeNormalizer,
		private AnalyserExpressionSource $analyserExpressionSource
	) {}

	/**
	 * @param MethodCallExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		$ret = $subExpressionAnalyser->analyse($expression->target, $variableScope);
		$retExpr = $ret->expressionType;

		$scope = $ret->variableScope;

		$retParam = $subExpressionAnalyser->analyse(
			$expression->parameter,
			$scope
		);

		$method = $this->methodFinder->findMethodFor(
			$retExpr,
			$expression->methodName,
			$retParam->expressionType,
			$subExpressionAnalyser
		);
		if ($method === NoMethodAvailable::value) {
			throw new AnalyserException(
				sprintf("No appropriate method found for %s->%s (type %s inferred)",
					$expression->target, $expression->methodName, $retExpr
				)
			);
		}
		return new ExecutionResultContext(
			$method->returnType->returnType,
			$this->unionTypeNormalizer->normalize(
				$ret->returnType,
				$retParam->returnType,
			),
			$this->unionTypeNormalizer->normalizeErrorType(
				$ret->errorType,
				$method->returnType->errorType,
				$retParam->errorType,
			),
			$retParam->variableScope,
			$method
		);
	}

	/**
	 * @param MethodCallExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$targetRet = $subExpressionExecutor->execute($expression->target, $variableValueScope);
		$retValue = $targetRet->value;
		$scope = $targetRet->globalScope;

		$retParam = $subExpressionExecutor->execute(
			$expression->parameter,
			$scope
		);
		$scope = $retParam->globalScope;

		$targetType = $this->analyserExpressionSource
			->getExpressionResult($expression->target)->expressionType;

		//$method = $this->analyserExpressionSource->getExpressionResult($expression)->contextData;
		try {
			$method = $this->analyserExpressionSource->getExpressionResult($expression)->contextData;
		} catch (AnalyserException) {
			$method = $this->methodFinder->findMethodFor(
				$targetType,
				$expression->methodName,
				$this->analyserExpressionSource->getExpressionResult(
					$expression->parameter
				)->expressionType,
				null
			);
		}
		if ($method instanceof FunctionValue) {
			$value = $this->functionExecutor->executeFunction(
				$subExpressionExecutor,
				$method,
				$retParam->value,
				target: $retValue,
				targetType: $targetType
			);
			return new ExecutionResultValueContext(
				$value,
				$scope
			);
		}
		throw new ExecutorException(
			sprintf("Illegal attempt to call method %s->%s",
				$expression->target, $expression->methodName)
		);
	}
}