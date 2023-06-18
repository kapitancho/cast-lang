<?php

namespace Cast\Service\Compiler;

use Cast\Model\Context\VariablePair;
use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\ContextCodeExpressionAnalyser;
use Cast\Service\Type\SubtypeRelationChecker;

final readonly class FunctionAnalyser {

	public function __construct(
		private ContextCodeExpressionAnalyser $contextCodeExpressionAnalyser,
		private SubtypeRelationChecker        $subtypeRelationChecker,
	) {}

	/** @throws AnalyserException */
	public function analyseFunction(VariableNameIdentifier $functionName, FunctionValue $fn): void {
		$vars = [];
		if ($fn->parameterType instanceof RecordType || $fn->parameterType instanceof TupleType) {
			foreach ($fn->parameterType->types as $paramName => $paramType) {
				$vars[] =
					new VariablePair(
						new VariableNameIdentifier('#' . $paramName),
						$paramType
					);
			}
		}
		$result = $this->contextCodeExpressionAnalyser->analyse(
			$fn->functionBody,
			VariableScope::fromVariablePairs(
				new VariablePair(
					new VariableNameIdentifier('#'),
					$fn->parameterType
				),
				... $vars
			)
		);
		if (!$this->subtypeRelationChecker->isSubtype(
			$result->returnType,
			$fn->returnType->returnType
		)) {
			throw new AnalyserException(sprintf(
				"The actual return type %s of %s is not a subtype of the declared return type %s\n",
				$result->returnType,
				$functionName,
				$fn->returnType->returnType
			));
		}
		if (
			$result->errorType->anyError &&
			!$fn->returnType->errorType->anyError
		) {
			throw new AnalyserException(sprintf(
				"The actual error type of %s can have any error and this is not declared\n",
				$functionName
			));
		}
		if (!$fn->returnType->errorType->anyError &&
			!$this->subtypeRelationChecker->isSubtype(
				$result->errorType->errorType,
				$fn->returnType->errorType->errorType
			)
		) {
			throw new AnalyserException(sprintf(
				"The actual error type %s of %s is not a subtype of the declared error type %s\n",
				$result->errorType->errorType,
				$functionName,
				$fn->returnType->errorType->errorType
			));
		}
	}

}