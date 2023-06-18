<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\TupleExpression;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @implements ExpressionAnalyser<TupleExpression>
 * @implements ExpressionExecutor<TupleExpression>
 */
final readonly class TupleExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private UnionTypeNormalizer $unionTypeNormalizer
	) {}

	/**
	 * @param TupleExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		$errorType = new ErrorType;
		$returnType = new NothingType;

		$subtypes = [];
		foreach($expression->values as $value) {
			$subRet = $subExpressionAnalyser->analyse($value, $variableScope);

			$subtypes[] = $subRet->expressionType;
			$variableScope = $subRet->variableScope;
			$errorType = $this->unionTypeNormalizer->normalizeErrorType($errorType, $subRet->errorType);
			$returnType = $this->unionTypeNormalizer->normalize($returnType, $subRet->returnType);
		}
		return new ExecutionResultContext(
			new TupleType(... $subtypes),
			$returnType,
			$errorType,
			$variableScope
		);
	}

	/**
	 * @param TupleExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$values = [];
		foreach($expression->values as $value) {
			$subRet = $subExpressionExecutor->execute($value, $variableValueScope);

			$values[] = $subRet->value;
			$variableValueScope = $subRet->globalScope;
		}
		return new ExecutionResultValueContext(
			new ListValue(... $values),
			$variableValueScope
		);
	}
}