<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\LoopExpression;
use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Term\LoopExpressionType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @implements ExpressionAnalyser<LoopExpression>
 * @implements ExpressionExecutor<LoopExpression>
 */
final readonly class LoopExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private UnionTypeNormalizer $unionTypeNormalizer,
	) {}

	/**
	 * @param LoopExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		$retCheck = $subExpressionAnalyser->analyse($expression->checkExpression, $variableScope);

		$scope = $retCheck->variableScope;
		$retLoop = $subExpressionAnalyser->analyse($expression->loopExpression, $scope);

		return new ExecutionResultContext(
			//new NothingType
			$this->unionTypeNormalizer->normalize(
				$retLoop->expressionType, ...
					$expression->loopExpressionType === LoopExpressionType::While ? [new NullType] : []
			),
			$this->unionTypeNormalizer->normalize(
				$retCheck->returnType, $retLoop->returnType
			),
			$this->unionTypeNormalizer->normalizeErrorType(
				$retCheck->errorType, $retLoop->errorType
			),
			$scope
		);
	}

	/**
	 * @param LoopExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$value = new LiteralValue(new NullLiteral);
		if ($expression->loopExpressionType === LoopExpressionType::While) {
			$retCheck = $subExpressionExecutor->execute($expression->checkExpression, $variableValueScope);
			$scope = $retCheck->globalScope;
			while($retCheck->value instanceof LiteralValue &&
				$retCheck->value->literal instanceof BooleanLiteral &&
				$retCheck->value->literal->value
			) {
				$retLoop = $subExpressionExecutor->execute($expression->loopExpression, $scope);
				$scope = $retLoop->globalScope;
				$value = $retLoop->value;
				$retCheck = $subExpressionExecutor->execute($expression->checkExpression, $scope);
				$scope = $retCheck->globalScope;
			}
			return new ExecutionResultValueContext($value, $scope);
		}

		$nextScope = $variableValueScope;
		do {
			$retLoop = $subExpressionExecutor->execute($expression->loopExpression, $nextScope);
			$scope = $retLoop->globalScope;
			$value = $retLoop->value;
			$retCheck = $subExpressionExecutor->execute($expression->checkExpression, $scope);
			$nextScope = $retCheck->globalScope;
		} while(
			$retCheck->value instanceof LiteralValue &&
			$retCheck->value->literal instanceof BooleanLiteral &&
			$retCheck->value->literal->value
		);
		return new ExecutionResultValueContext($value, $scope);
	}

}