<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\ReturnExpression;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\Flow\ReturnResult;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @implements ExpressionAnalyser<ReturnExpression>
 * @implements ExpressionExecutor<ReturnExpression>
 */
final readonly class ReturnExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private UnionTypeNormalizer $unionTypeNormalizer
	) {}

	/**
	 * @param ReturnExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		$ret = $subExpressionAnalyser->analyse($expression->returnedValue, $variableScope);
		return $ret
			->withExpressionType(new NothingType)
			->withReturnType(
				$this->unionTypeNormalizer->normalize(
					$ret->returnType,
					$ret->expressionType
				)
			);
	}

	/**
	 * @param ReturnExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return never
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): never {
		throw new ReturnResult(
			$subExpressionExecutor
				->execute($expression->returnedValue, $variableValueScope)
				->value
		);
	}
}