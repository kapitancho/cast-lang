<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\RecordExpression;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @implements ExpressionAnalyser<RecordExpression>
 * @implements ExpressionExecutor<RecordExpression>
 */
final readonly class RecordExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private UnionTypeNormalizer $unionTypeNormalizer
	) {}

	/**
	 * @param RecordExpression $expression
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
		foreach($expression->values as $name => $value) {
			$subRet = $subExpressionAnalyser->analyse($value, $variableScope);

			$subtypes[$name] = $subRet->expressionType;
			$variableScope = $subRet->variableScope;
			$errorType = $this->unionTypeNormalizer->normalizeErrorType($errorType, $subRet->errorType);
			$returnType = $this->unionTypeNormalizer->normalize($returnType, $subRet->returnType);
		}
		return new ExecutionResultContext(
			new RecordType(... $subtypes),
			$returnType,
			$errorType,
			$variableScope
		);
	}

	/**
	 * @param RecordExpression $expression
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
		foreach($expression->values as $name => $value) {
			$subRet = $subExpressionExecutor->execute($value, $variableValueScope);

			$values[$name] = $subRet->value;
			$variableValueScope = $subRet->globalScope;
		}
		return new ExecutionResultValueContext(
			new DictValue($values),
			$variableValueScope
		);
	}
}