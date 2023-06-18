<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\TypedValue;
use Cast\Model\Context\VariableValuePair;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Expression\SequenceExpression;
use Cast\Model\Program\Expression\VariableNameExpression;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\TypeCast;
use Cast\Model\Runtime\Value\ClosureValue;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Execution\Flow\ThrowResult;
use Cast\Service\Type\TypeAssistant;

final readonly class FunctionExecutor {

	public function __construct(
		private TypeAssistant $typeAssistant,
	) {}

	public function executeConstructor(
		SubExpressionExecutor $subExpressionExecutor,
		SubtypeType $subtype,
		Value $parameter,
	): Value {
		$value = $this->executeFunction(
			$subExpressionExecutor,
			new FunctionValue(
				$subtype->baseType(),
				new FunctionReturnType(
					$this->typeAssistant->typeByName(new TypeNameIdentifier('Any')),
					$subtype->errorType()
				),
				new FunctionBodyExpression(
					new SequenceExpression(
						$subtype->functionBody()->body,
						new VariableNameExpression(new VariableNameIdentifier('#'))
					)
				)
			),
			$parameter,
		);
		return new SubtypeValue(
			$subtype->typeName(),
			$value
		);
	}

	public function executeCastFunction(
		SubExpressionExecutor $subExpressionExecutor,
		TypeCast $typeCast,
		Value $parameter,
	): Value {
		$fromType = $this->typeAssistant->typeByName($typeCast->castFromType);
		return $this->executeFunction(
			$subExpressionExecutor,
			new FunctionValue(
				$fromType,
				new FunctionReturnType(
					$this->typeAssistant->typeByName($typeCast->castToType),
					$typeCast->errorType
				),
				$typeCast->functionBody,
			),
			new LiteralValue(new NullLiteral),
			target: $parameter,
			targetType: $fromType
		);
	}

	/** @throws ExecutorException|ThrowResult */
	public function executeFunction(
		SubExpressionExecutor      $subExpressionExecutor,
		ClosureValue|FunctionValue $function,
		Value                      $parameter,
		VariableValueScope         $scope = null,
		Value|null                 $target = null,
		Type|null                  $targetType = null,
	): Value {
		$fn = $function instanceof ClosureValue ? $function->function : $function;

		//convert [] to [:]
		$basicParameterType = $this->typeAssistant->toBasicType($fn->parameterType);
		if ($basicParameterType instanceof RecordType && $parameter instanceof ListValue &&
			count($parameter->items) === count($basicParameterType->types)
		) {
			$pValues = [];
			$abIndex = 0;
			foreach($basicParameterType->types as $k => $v) {
				$pValues[$k] = $parameter->items[$abIndex++];
			}
			$parameter = new DictValue($pValues);
		}

		$vars = [];
		if ($function instanceof ClosureValue) {
			$vars = array_values($function->globalScope->scope);
		}
		$vars[] = new VariableValuePair(
			new VariableNameIdentifier('#'),
			new TypedValue($fn->parameterType, $parameter),
		);
		if ($target) {
			$vars[] = new VariableValuePair(
				new VariableNameIdentifier('$'),
				new TypedValue($targetType, $target),
			);
		}

		if ($fn->parameterType instanceof RecordType || $fn->parameterType instanceof TupleType) {
			$v = $parameter;
			while($v instanceof SubtypeValue) {
				$v = $v->baseValue;
			}
			if ($v instanceof DictValue || $v instanceof ListValue) {
				foreach($fn->parameterType->types as $paramName => $paramType) {
					$vars[] = new VariableValuePair(
						new VariableNameIdentifier('#' . $paramName),
						new TypedValue(
							$paramType,
							$v->items[$paramName] ??
								throw new ExecutorException(sprintf(
									"Expected property %s has not been found",
									$paramName
								))
						),
					);
				}
			}
		}
		$scope ??= VariableValueScope::empty();
		return $subExpressionExecutor->execute(
			$fn->functionBody,
			$scope->withAddedValues(... $vars)
		)->value;
	}
}