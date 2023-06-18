<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\CatchExpression;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Execution\Flow\ThrowResult;
use Cast\Service\Expression\AnalyserExpressionSource;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\UnionTypeNormalizer;
use Cast\Service\Type\ValueTypeResolver;

/**
 * @implements ExpressionAnalyser<CatchExpression>
 * @implements ExpressionExecutor<CatchExpression>
 */
final readonly class CatchExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private TypeAssistant $typeAssistant,
		private ValueTypeResolver      $valueTypeResolver,
		private SubtypeRelationChecker $subtypeRelationChecker,
		private UnionTypeNormalizer    $unionTypeNormalizer,
		private AnalyserExpressionSource $analyserExpressionSource
	) {}

	/**
	 * @param CatchExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		$ret = $subExpressionAnalyser->analyse(
			$expression->catchTarget, $variableScope
		);
		return $ret
			->withExpressionType(
				$this->unionTypeNormalizer->normalize(
					$ret->expressionType,
					$ret->errorType->errorType,
					$expression->anyType && $ret->errorType->anyError ?
						$this->typeAssistant->typeByName(new TypeNameIdentifier("RuntimeError")) :
						new NothingType,
				)
			)->withErrorType(
				new ErrorType(!$expression->anyType && $ret->errorType->anyError)
			);
	}

	/**
	 * @param CatchExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		try {
			return $subExpressionExecutor->execute(
				$expression->catchTarget, $variableValueScope
			);
		} catch (ThrowResult $throwResult) {
			$t = $this->analyserExpressionSource->getExpressionResult(
				$expression->catchTarget
			);
			$exceptionType = $this->valueTypeResolver->findTypeOf($throwResult->v);

			if ($expression->anyType) {
				return new ExecutionResultValueContext(
					new SubtypeValue(
						new TypeNameIdentifier("RuntimeError"),
						new DictValue(
							['error' => $throwResult->v]
						)
					),
					$variableValueScope
				);
			}
			if ($this->subtypeRelationChecker->isSubtype(
				$exceptionType, $t->errorType->errorType
			)) {
				return new ExecutionResultValueContext(
					$throwResult->v,
					$variableValueScope
				);
			}
			throw $throwResult;
		}
	}
}