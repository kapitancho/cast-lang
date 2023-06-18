<?php

namespace Cast\Service\Expression\Behaviour;

use Cast\Model\Context\VariableScope;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\PropertyAccessExpression;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Service\Execution\ExecutionResultContext;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\ExecutorException;
use Cast\Service\Expression\ExpressionAnalyser;
use Cast\Service\Expression\ExpressionExecutor;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Type\PropertyTypeFinder;
use Cast\Service\Type\TypeException;

/**
 * @implements ExpressionAnalyser<PropertyAccessExpression>
 * @implements ExpressionExecutor<PropertyAccessExpression>
 */
final readonly class PropertyAccessExpressionBehaviour implements ExpressionAnalyser, ExpressionExecutor {
	public function __construct(
		private PropertyTypeFinder $propertyTypeFinder,
	) {}

	/**
	 * @param PropertyAccessExpression $expression
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
		$retType = $ret->expressionType;

		try {
			return $ret->withExpressionType(
				$this->propertyTypeFinder->findPropertyType($retType, $expression->propertyName)
			);
		} catch (TypeException) {
			throw new AnalyserException(
				sprintf("Unknown property %s for type %s",
					$expression->propertyName,
						$retType
				)
			);
		}
	}

	/**
	 * @param PropertyAccessExpression $expression
	 * @param VariableValueScope $variableValueScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Expression            $expression,
		VariableValueScope    $variableValueScope,
		SubExpressionExecutor $subExpressionExecutor
	): ExecutionResultValueContext {
		$ret = $subExpressionExecutor->execute($expression->target, $variableValueScope);
		$retValue = $ret->value;
		while ($retValue instanceof SubtypeValue) {
			$retValue = $retValue->baseValue;
		}
		if ($retValue instanceof ListValue) {
			if (is_numeric($expression->propertyName->identifier)) {
				if (array_key_exists($expression->propertyName->identifier, $retValue->items)) {
					return new ExecutionResultValueContext(
						$retValue->items[$expression->propertyName->identifier],
						$ret->globalScope
					);
				}
			}
		} elseif ($retValue instanceof DictValue) {
			if (array_key_exists($expression->propertyName->identifier, $retValue->items)) {
				return new ExecutionResultValueContext(
					$retValue->items[$expression->propertyName->identifier],
					$ret->globalScope
				);
			}
		}
		throw new ExecutorException(
			sprintf(
				"Cannot access property of a value that is no tuple or record: %s, %s given",
				$expression,
				$retValue
			)
		);
	}
}