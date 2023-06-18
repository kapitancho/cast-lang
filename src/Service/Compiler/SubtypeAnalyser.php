<?php

namespace Cast\Service\Compiler;

use Cast\Model\Context\VariablePair;
use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\ContextCodeExpressionAnalyser;
use Cast\Service\Type\SubtypeRelationChecker;

final readonly class SubtypeAnalyser {

	public function __construct(
		private ContextCodeExpressionAnalyser $contextCodeExpressionAnalyser,
		private SubtypeRelationChecker $subtypeRelationChecker,
	) {}

	/** @throws AnalyserException */
	public function analyseSubtype(SubtypeType $subtypeDefinition): void {
		$vars = [];
		$baseType = $subtypeDefinition->baseType();
		if ($baseType instanceof RecordType || $baseType instanceof TupleType) {
			foreach ($baseType->types as $paramName => $paramType) {
				$vars[] =
					new VariablePair(
						new VariableNameIdentifier('#' . $paramName),
						$paramType
					);
			}
		}

		$result = $this->contextCodeExpressionAnalyser->analyse(
			$subtypeDefinition->functionBody(),
			VariableScope::fromVariablePairs(
				new VariablePair(
					new VariableNameIdentifier('#'),
					$baseType
				),
				... $vars
			)
		);
		if (
			$result->errorType->anyError &&
			!$subtypeDefinition->errorType()->anyError
		) {
			throw new AnalyserException(sprintf(
				"The actual error type of %s can have any error and this is not declared\n",
				$subtypeDefinition->typeName()
			));
		}
		if (!$subtypeDefinition->errorType()->anyError &&
			!$this->subtypeRelationChecker->isSubtype(
				$result->errorType->errorType,
				$subtypeDefinition->errorType()->errorType
			)
		) {
			throw new AnalyserException(sprintf(
				"The actual error type %s of %s is not a subtype of the declared error type %s\n",
				$result->errorType->errorType,
				$subtypeDefinition->typeName(),
				$subtypeDefinition->errorType()->errorType
			));
		}
	}

}