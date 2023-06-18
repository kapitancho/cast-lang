<?php

namespace Cast\Model\Program\TypeDefinition;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Node;
use Cast\Model\Program\Type\TypeTerm;

final readonly class AliasTypeDefinitionTerm implements Node /*implements Type, ProgramNode*/ {
	public function __construct(
		public TypeNameIdentifier $typeName,
		public TypeTerm $aliasedType
	) {}

	public function __toString(): string {
		return sprintf("%s = %s",  $this->typeName, $this->aliasedType);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_definition_term',
			'type' => 'alias',
			'type_name' => $this->typeName,
			'aliased_type' => $this->aliasedType
		];
	}
}