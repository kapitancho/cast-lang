<?php

namespace Cast\Service\Program;

use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Program;
use Cast\Model\Program\ProgramModule;
use Cast\Model\Program\Term\Expression\TypeCastTerm;
use Cast\Model\Runtime\TypeCast;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Registry\NestedCastRegistry;
use Cast\Service\Transformer\TermToExpressionConverter;
use Cast\Service\Transformer\TermToTypeTransformer;

final readonly class CastRegistryBuilder {
	public function __construct(
		private TermToTypeTransformer $termToTypeTransformer,
		private TermToExpressionConverter $termToExpressionConverter
	) {}

	public function buildForProgram(Program $program, CastRegistry $builtInCastRegistry): NestedCastRegistry {
		$casts = $this->getCastsForModules($program->modules);
		return new NestedCastRegistry($builtInCastRegistry, ...$casts);
	}

	public function buildForModule(ProgramModule $programModule, CastRegistry $builtInCastRegistry): NestedCastRegistry {
		$casts = $this->getCastsForModules([$programModule]);
		return new NestedCastRegistry($builtInCastRegistry, ...$casts);
	}

	/**
	 * @param ProgramModule[] $programModules
	 * @return TypeCastTerm[]
	 */
	private function getCastsForModules(array $programModules): array {
		$casts = [];
		foreach($programModules as $programModule) {
			foreach($programModule->expressions() as $moduleExpression) {
				$e = $moduleExpression->expression;
				match($e::class) {
					TypeCastTerm::class => $casts[] = new TypeCast(
						$e->castFromType->typeName,
						$e->castToType->typeName,
						new FunctionBodyExpression(
							$this->termToExpressionConverter
								->termToExpression($e->functionBody->body)
						),
						$this->termToTypeTransformer->termToErrorType($e->errorType)
					),
					default => null
				};
			}
		}
		return $casts;
	}

}