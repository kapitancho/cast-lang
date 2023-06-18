<?php

namespace Cast\Service\Builder;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Program;
use Cast\Service\Compiler\ProgramContext;
use Cast\Service\Program\CastRegistryBuilder;
use Cast\Service\Program\GlobalScopeBuilder;
use Cast\Service\Registry\BuiltInTypeRegistry;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Registry\EmptyCastRegistry;
use Cast\Service\Transformer\ConstantToValueConverter;
use Cast\Service\Transformer\ProgramTransformer;
use Cast\Service\Transformer\TermToExpressionConverter;
use Cast\Service\Transformer\TermToTypeTransformer;
use Cast\Service\Type\ValueTypeResolver;

final readonly class ProgramContextBuilder {
	private function buildGlobalScope(
		Program $program,
		ConstantToValueConverter $constantToValueConverter,
		ValueTypeResolver $valueTypeResolver
	): VariableValueScope {
		$globalScopeBuilder = new GlobalScopeBuilder(
			$constantToValueConverter,
			$valueTypeResolver,
		);
		return $globalScopeBuilder->buildForProgram($program, VariableValueScope::empty());
	}

	private function buildCastRegistry(
		Program $program,
		TermToTypeTransformer $termToTypeTransformer,
		TermToExpressionConverter $termToExpressionConverter
	): CastRegistry {
		$nativeCastRegistry = new EmptyCastRegistry;
		return (new CastRegistryBuilder(
			$termToTypeTransformer,
			$termToExpressionConverter
		))->buildForProgram(
			$program,
			$nativeCastRegistry
		);
	}

	public function buildForProgram(Program $program): ProgramContext {
		$transformer = new ProgramTransformer($program, new BuiltInTypeRegistry);
		return new ProgramContext(
			$transformer,
			$valueTypeResolver = new ValueTypeResolver($transformer),
			$this->buildGlobalScope(
				$program,
				$transformer,
				$valueTypeResolver
			),
			$this->buildCastRegistry(
				$program,
				$transformer,
				$transformer
			)
		);
	}

}