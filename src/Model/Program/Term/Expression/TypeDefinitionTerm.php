<?php

namespace Cast\Model\Program\Term\Expression;

use Cast\Model\Program\ProgramNode;
use Cast\Model\Program\TypeDefinition\AliasTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\EnumerationTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\SubtypeDefinitionTerm;

final readonly class TypeDefinitionTerm implements ProgramNode {
	public function __construct(
		public AliasTypeDefinitionTerm|EnumerationTypeDefinitionTerm|SubtypeDefinitionTerm $type,
	) {}

	public function __toString(): string {
		return (string)$this->type;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'module_expression',
			'expression_type' => 'type_definition_term',
			'type_definition' => $this->type
		];
	}
}