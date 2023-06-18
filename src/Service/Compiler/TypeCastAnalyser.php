<?php

namespace Cast\Service\Compiler;

use Cast\Model\Context\VariablePair;
use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\TypeCast;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\ContextCodeExpressionAnalyser;
use Cast\Service\Transformer\TypeRegistry;
use Cast\Service\Type\SubtypeRelationChecker;

final readonly class TypeCastAnalyser {

	public function __construct(
		private TypeRegistry                  $typeByNameFinder,
		private ContextCodeExpressionAnalyser $contextCodeExpressionAnalyser,
		private SubtypeRelationChecker        $subtypeRelationChecker,
	) {}

	/** @throws AnalyserException */
	public function analyseTypeCast(TypeCast $typeCast): void {
		$result = $this->contextCodeExpressionAnalyser->analyse(
			$typeCast->functionBody,
			VariableScope::fromVariablePairs(
				new VariablePair(
					new VariableNameIdentifier('$'),
					$this->typeByNameFinder->typeByName($typeCast->castFromType)
				)
			)
		);
		if (!$this->subtypeRelationChecker->isSubtype(
			$result->returnType,
			$this->typeByNameFinder->typeByName($typeCast->castToType)
		)) {
			throw new AnalyserException(sprintf(
				"The actual return type %s of %s ==> %s is not a subtype of the declared return type %s\n",
				$result->returnType,
				$typeCast->castFromType,
				$typeCast->castToType,
				$typeCast->castToType,
			));
		}
		if (
			$result->errorType->anyError &&
			!$typeCast->errorType->anyError
		) {
			throw new AnalyserException(sprintf(
				"The actual error type of %s ==> %s can have any error and this is not declared\n",
				$typeCast->castFromType,
				$typeCast->castToType,
			));
		}
		if (!$typeCast->errorType->anyError &&
			!$this->subtypeRelationChecker->isSubtype(
				$result->errorType->errorType,
				$typeCast->errorType->errorType
			)
		) {
			throw new AnalyserException(sprintf(
				"The actual error type %s is not a subtype of the declared error type %s\n",
				$result->errorType->errorType,
				$typeCast->errorType->errorType
			));
		}
	}

}