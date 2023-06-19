<?php /** @noinspection TypeUnsafeComparisonInspection */

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariablePair;
use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\MatchExpression;
use Cast\Model\Program\Expression\MatchExpressionOperation;
use Cast\Model\Program\Expression\VariableAssignmentExpression;
use Cast\Model\Program\Expression\VariableNameExpression;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\UnionTypeNormalizer;
use Cast\Service\Type\ValueTypeResolver;

/**
 * @implements ExpressionAnalyser<MatchExpression>
 * @implements ExpressionExecutor<MatchExpression>
 */
final readonly class MatchExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private UnionTypeNormalizer $unionTypeNormalizer,
		private ValueTypeResolver      $valueTypeResolver,
		private SubtypeRelationChecker $subtypeRelationChecker
	) {}

	/**
	 * @param MatchExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser,
	): ExecutionResultContext {
		$retTarget = $subExpressionAnalyser->analyse($expression->target, $variableScope);

		$scope = $retTarget->variableScope;

		$expressionTypes = [];
		$returnTypes = [$retTarget->returnType];
		$errorTypes = [$retTarget->errorType];
		//$scopes = [];

		foreach($expression->parameters as $expr) {
			$retMatched = $subExpressionAnalyser->analyse(
				$expr->matchedExpression,
				$scope
			);
			$returnTypes[] = $retMatched->returnType;
			$errorTypes[] = $retMatched->errorType;

			$innerScope = $scope;

			if ($expression->operation === MatchExpressionOperation::isSubtypeOf && (
				$expression->target instanceof VariableAssignmentExpression ||
				$expression->target instanceof VariableNameExpression
 			) && $expr->matchedExpression instanceof ConstantExpression &&
				$expr->matchedExpression->constant instanceof TypeValue &&
				!($expr->matchedExpression->constant->type instanceof AnyType)
			) {
				$innerScope = $innerScope->withAddedVariablePairs(
					new VariablePair(
						$expression->target->variableName,
						$expr->matchedExpression->constant->type
					)
				);
			}

			$ret = $subExpressionAnalyser->analyse(
				$expr->valueExpression,
				$innerScope
			);
			//$scopes[] = $ret->variableScope;
			$expressionTypes[] = $ret->expressionType;
			$returnTypes[] = $ret->returnType;
			$errorTypes[] = $ret->errorType;
		}
		//@TODO - use scopes
		return new ExecutionResultContext(
			$this->unionTypeNormalizer->normalize(... $expressionTypes),
			$this->unionTypeNormalizer->normalize(... $returnTypes),
			$this->unionTypeNormalizer->normalizeErrorType(... $errorTypes),
			$scope
		);
	}

	/**
	 * @param MatchExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$retTarget = $subExpressionExecutor->execute($expression->target, $variableValueScope);

		$scope = $retTarget->globalScope;
		foreach($expression->parameters as $expr) {
			if ($expression->operation === MatchExpressionOperation::equals) {
				$retMatched = $subExpressionExecutor->execute(
					$expr->matchedExpression,
					$scope
				);
				$matchedValue = $retMatched->value;
				$scope = $retMatched->globalScope;

				if ($retTarget->value == $matchedValue) { //TODO - binary equals
					return $subExpressionExecutor->execute(
						$expr->valueExpression,
						$scope
					);
				}
			} elseif (
				$expression->operation === MatchExpressionOperation::isSubtypeOf &&
				$expr->matchedExpression instanceof ConstantExpression &&
				$expr->matchedExpression->constant instanceof TypeValue
			) {
				$valueType = $this->valueTypeResolver->findTypeOf($retTarget->value);

				if ($this->subtypeRelationChecker->isSubtype(
					$valueType,
					$expr->matchedExpression->constant->type
				)) {
					return $subExpressionExecutor->execute(
						$expr->valueExpression,
						$scope
					);
				}
			}
		}
		return new ExecutionResultValueContext(
			new LiteralValue(new NullLiteral),
			$scope
		);
	}
}