<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\FunctionCallExpression;
use Cast\Model\Program\Expression\MethodCallExpression;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FunctionType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Value\ClosureValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TupleToRecordTypeConverter;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @implements ExpressionAnalyser<FunctionCallExpression>
 * @implements ExpressionExecutor<FunctionCallExpression>
 */
final readonly class FunctionCallExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private FunctionExecutor $functionExecutor,
		private UnionTypeNormalizer $unionTypeNormalizer,
		private SubtypeRelationChecker $subtypeRelationChecker,
		private MethodCallExpressionBehaviour $methodCallExpressionBehaviour,
		private TupleToRecordTypeConverter $tupleToRecordTypeConverter,
	) {}

	/**
	 * @param FunctionCallExpression $expression
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

		while ($retExpr instanceof AliasType) {
			$retExpr = $retExpr->aliasedType();
		}

		if (!($retExpr instanceof FunctionType)) {
			return $this->methodCallExpressionBehaviour->analyse(
				new MethodCallExpression(
					$expression->target,
					new PropertyNameIdentifier("invoke"),
					$expression->parameter,
				),
				$variableScope,
				$subExpressionAnalyser
			);
			/*
			throw new AnalyserException(
				sprintf("The expression %s is not a function", $expression->target)
			);
			*/
		}
		$retParam = $subExpressionAnalyser->analyse(
			$expression->parameter,
			$scope
		);

		$retParamType = $retParam->expressionType;

		$fn = $retExpr;

		if ($fn->parameterType instanceof RecordType) {
			$retParamType = $this->tupleToRecordTypeConverter->tryToRecordType($retParamType, $fn->parameterType);
		}
		/*if ($fn->parameterType instanceof RecordType) {
			$abc = $retParamType instanceof IntersectionType ?
				$retParamType->types : [$retParamType];
			foreach($abc as $ab) {
				if ($ab instanceof TupleType && count($ab->types) === count($fn->parameterType->types)) {
					$pTypes = [];
					$abIndex = 0;
					foreach($fn->parameterType->types as $k => $v) {
						$pTypes[$k] = $ab->types[$abIndex++];
					}
					$retParamType = new RecordType(... $pTypes);
				}
			}
		}*/

		if (!$this->subtypeRelationChecker->isSubtype(
			$retParamType,
			$fn->parameterType,
		)) {
			throw new AnalyserException(
				sprintf(
					"Cannot pass a parameter of type %s to a function expecting a parameter of type %s",
					$retParamType,
					$fn->parameterType,
				)
			);
		}

		$callCastParamType = new NothingType;
		$callCastErrorType = new ErrorType;
		$returnCastParamType = new NothingType;
		$returnCastErrorType = new ErrorType;
		//@TODO - validate
		return new ExecutionResultContext(
			$fn->returnType->returnType,
			$this->unionTypeNormalizer->normalize(
				$ret->returnType,
				$callCastParamType,
				$retParam->returnType,
				$returnCastParamType,
				//$fn->returnType->returnType
			),
			$this->unionTypeNormalizer->normalizeErrorType(
				$ret->errorType,
				$callCastErrorType,
				$retParam->errorType,
				$returnCastErrorType,
				$fn->returnType->errorType
			),
			$retParam->variableScope
		);
	}

	/**
	 * @param FunctionCallExpression $expression
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
		if (!($retValue instanceof FunctionValue || $retValue instanceof ClosureValue)) {
			return $this->methodCallExpressionBehaviour->execute(
				new MethodCallExpression(
					$expression->target,
					new PropertyNameIdentifier("invoke"),
					$expression->parameter,
				),
				$variableValueScope,
				$subExpressionExecutor
			);
			/*
			throw new AnalyserException(
				sprintf("The expression %s is not a function", $expression->target)
			);
			*/
		}
		$scope = $targetRet->globalScope;

		$retParam = $subExpressionExecutor->execute(
			$expression->parameter,
			$scope
		);
		$retParamValue = $retParam->value;
		$scope = $retParam->globalScope;

		$value = $this->functionExecutor->executeFunction(
			$subExpressionExecutor,
			$retValue,
			$retParamValue,
			$variableValueScope
		);
		return new ExecutionResultValueContext(
			$value,
			$scope
		);
	}
}