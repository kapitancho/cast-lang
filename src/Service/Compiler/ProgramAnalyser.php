<?php

namespace Cast\Service\Compiler;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Transformer\TypeRegistry;

final readonly class ProgramAnalyser {

	public function __construct(
		private FunctionAnalyser $functionAnalyser,
		private TypeCastAnalyser $typeCastAnalyser,
		private SubtypeAnalyser  $subtypeAnalyser,
	) {}

	private function analyseGlobalScope(VariableValueScope $globalScope): void {
		foreach($globalScope->scope as $globalScopeValue) {
			$v = $globalScopeValue->typedValue->value;
			if ($v instanceof FunctionValue) {
				$this->functionAnalyser->analyseFunction(
					$globalScopeValue->variableName,
					$v
				);
			}
		}
	}

	private function analyseCastRegistry(CastRegistry $castRegistry): void {
		foreach($castRegistry->getAllCasts() as $typeCast) {
			$this->typeCastAnalyser->analyseTypeCast($typeCast);
		}
	}

	private function analyseTypeRegistry(TypeRegistry $typeRegistry): void {
		foreach($typeRegistry->allTypes() as $type) {
			if ($type instanceof SubtypeType) {
				$this->subtypeAnalyser->analyseSubtype($type);
			}
		}
	}

	/** @throws AnalyserException */
	public function analyseProgram(ProgramContext $programContext): void {
		$this->analyseGlobalScope($programContext->globalScope);
		$this->analyseCastRegistry($programContext->castRegistry);
		$this->analyseTypeRegistry($programContext->typeByNameFinder);
	}

}