<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\ConstructorCallExpression;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\MutableType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Value\MutableValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\ExecutorException;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @implements ExpressionAnalyser<ConstructorCallExpression>
 * @implements ExpressionExecutor<ConstructorCallExpression>
 */
final readonly class ConstructorCallExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private FunctionExecutor $functionExecutor,
		private TypeAssistant  $typeAssistant,
		private UnionTypeNormalizer    $unionTypeNormalizer,
		private SubtypeRelationChecker $subtypeRelationChecker
	) {}

	/**
	 * @param ConstructorCallExpression $expression
	 * @param VariableScope $variableScope
	 * @param SubExpressionAnalyser $subExpressionAnalyser
	 * @return ExecutionResultContext
	 */
	public function analyse(
		Expression                  $expression,
		VariableScope         $variableScope,
		SubExpressionAnalyser $subExpressionAnalyser
	): ExecutionResultContext {
		$retParam = $subExpressionAnalyser->analyse(
			$expression->parameter,
			$variableScope
		);
		$callCastParamType = new NothingType;
		$callCastErrorType = new ErrorType;
		$returnCastParamType = new NothingType;
		$returnCastErrorType = new ErrorType;
		$returnErrorType = new ErrorType;

		$retParamType = $retParam->expressionType;

		$expressionType = $this->typeAssistant->followAliases($expression->type);

		if ($expressionType instanceof MutableType) {
			if (
				$retParamType instanceof TupleType &&
				count($retParamType->types) > 0 &&
				$retParamType->types[0] instanceof TypeType
			) {
				$expressionType = new MutableType($retParamType->types[0]->refType);
			}
		}
		if ($expressionType instanceof SubtypeType) {
			$baseType = $expressionType->baseType();
			if ($baseType instanceof RecordType) {
				$abc = $retParamType instanceof IntersectionType ?
					$retParamType->types : [$retParamType];
				foreach($abc as $ab) {
					if ($ab instanceof TupleType && count($ab->types) ===
						count($baseType->types)
					) {
						$pTypes = [];
						$abIndex = 0;
						foreach($baseType->types as $k => $v) {
							$pTypes[$k] = $ab->types[$abIndex++];
						}
						$retParamType = new RecordType(... $pTypes);
					}
				}
			}
			if (!$this->subtypeRelationChecker->isSubtype(
				$retParamType,
				$baseType
			)) {
				throw new AnalyserException(
					sprintf(
						"The parameter to %s is of type %s and not of the expected type %s",
						$expressionType->typeName(),
						$retParamType,
						$baseType
					)
				);
			}
			$returnErrorType = $expressionType->errorType();
		}
		return new ExecutionResultContext(
			$expressionType,
			$this->unionTypeNormalizer->normalize(
				$callCastParamType,
				$retParam->returnType,
				$returnCastParamType,
			),
			$this->unionTypeNormalizer->normalizeErrorType(
				$callCastErrorType,
				$retParam->errorType,
				$returnErrorType,
				$returnCastErrorType,
			),
			$retParam->variableScope
		);
	}

	/**
	 * @param ConstructorCallExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$retParam = $subExpressionExecutor->execute(
			$expression->parameter,
			$variableValueScope
		);

		$retParamValue = $retParam->value;

		$type = $this->typeAssistant->followAliases($expression->type);

		if ($type instanceof MutableType) {
			$value = new MutableValue(
				$retParamValue->items[0]->type,
				$retParamValue->items[1]
			);
			/*$this->mutableStore->storeExpressionResult(
				$value, $retParamValue->items[1]
			);*/
			return $retParam->withValue($value);
		}
		if ($type instanceof SubtypeType) {
			$value = $this->functionExecutor->executeConstructor(
				$subExpressionExecutor,
				$type,
				$retParamValue
			);
			return $retParam->withValue($value);
		}
		throw new ExecutorException(
			sprintf("Illegal attempt to call a constructor of an incompatible type %s",
				$expression->type)
		);
	}
}