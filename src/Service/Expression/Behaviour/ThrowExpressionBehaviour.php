<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\ThrowExpression;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\Flow\ThrowResult;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @implements ExpressionAnalyser<ThrowExpression>
 * @implements ExpressionExecutor<ThrowExpression>
 */
final readonly class ThrowExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private UnionTypeNormalizer $unionTypeNormalizer
	) {}

	/**
	 * @param ThrowExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		$ret = $subExpressionAnalyser->analyse($expression->thrownValue, $variableScope);
		return $ret
			->withExpressionType(new NothingType)
			->withErrorType(
				$this->unionTypeNormalizer->normalizeErrorType(
					$ret->errorType,
					new ErrorType(errorType: $ret->expressionType)
				)
			);
	}

	/**
	 * @param ThrowExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return never
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): never {
		throw new ThrowResult(
			$subExpressionExecutor
				->execute($expression->thrownValue, $variableValueScope)
				->value
		);
	}
}