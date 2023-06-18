<?php

namespace Cast\Service\Program;

use Cast\Model\Context\TypedValue;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Context\VariableValuePair;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Program\Program;
use Cast\Model\Program\ProgramModule;
use Cast\Model\Program\Term\Expression\ConstantAssignmentTerm;
use Cast\Service\Transformer\ConstantToValueConverter;
use Cast\Service\Type\ValueTypeResolver;

final readonly class GlobalScopeBuilder {
	public function __construct(
		private ConstantToValueConverter $constantToValueConverter,
		private ValueTypeResolver $valueTypeResolver
	) {}

	public function buildForProgram(Program $program, VariableValueScope $builtInScope): VariableValueScope {
		return $builtInScope->withAddedValues(...
			$this->getValuesForModules($program->modules)
		);
	}

	public function buildForModule(ProgramModule $programModule, VariableValueScope $builtInScope): VariableValueScope {
		return $builtInScope->withAddedValues(...
			$this->getValuesForModules([$programModule])
		);
	}

	/**
	 * @param ProgramModule[] $programModules
	 * @return VariableValuePair[]
	 */
	public function getValuesForModules(array $programModules): array {
		$values = [];
		foreach($programModules as $programModule) {
			foreach($programModule->expressions() as $moduleExpression) {
				$e = $moduleExpression->expression;
				match($e::class) {
					ConstantAssignmentTerm::class => $values[] =
						new VariableValuePair(
							new VariableNameIdentifier($e->variableName),
							new TypedValue(
								$this->valueTypeResolver->findTypeOf(
									$value = $this->constantToValueConverter
										->convertConstantToValue($e->constant)
									),
								$value
							)
						),
					default => null
				};
			}
		}
		return $values;
	}
}