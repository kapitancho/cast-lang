<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\ReturnExpression;
use Cast\Model\Program\Expression\SequenceExpression;
use Cast\Model\Program\Expression\ThrowExpression;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @implements ExpressionAnalyser<SequenceExpression>
 * @implements ExpressionExecutor<SequenceExpression>
 */
final readonly class SequenceExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private UnionTypeNormalizer $unionTypeNormalizer
	) {}

	/**
	 * @param SequenceExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		$scope = $variableScope;

		$returnTypes = [];
		$errorTypes = [];
		$expressionType = new NothingType;

		foreach($expression->expressions as $expr) {
			$ret = $subExpressionAnalyser->analyse(
				$expr,
				$scope
			);
			$scope = $ret->variableScope;
			$expressionType = $ret->expressionType;
			$returnTypes[] = $ret->returnType;
			$errorTypes[] = $ret->errorType;
			if ($expr instanceof ReturnExpression) {
				break;
			}
			if ($expr instanceof ThrowExpression) {
				break;
			}
		}
		return new ExecutionResultContext(
			$expressionType,
			$this->unionTypeNormalizer->normalize(... $returnTypes),
			$this->unionTypeNormalizer->normalizeErrorType(... $errorTypes),
			$scope
		);
	}

	/**
	 * @param SequenceExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$scope = $variableValueScope;

		$value = new LiteralValue(new NullLiteral);
		foreach($expression->expressions as $expr) {
			$ret = $subExpressionExecutor->execute(
				$expr,
				$scope
			);
			$scope = $ret->globalScope;
			$value = $ret->value;
		}
		return new ExecutionResultValueContext(
			$value,
			$scope
		);
	}
}