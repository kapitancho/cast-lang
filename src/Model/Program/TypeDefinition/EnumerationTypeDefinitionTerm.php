<?php

namespace Cast\Model\Program\TypeDefinition;

use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Node;

final readonly class EnumerationTypeDefinitionTerm implements Node /*implements ResolvedType, ProgramNode*/ {
	/** @var array<string, EnumValueIdentifier> $values */
	public array $values;

	public function __construct(
		public TypeNameIdentifier $typeName,
		EnumValueIdentifier ... $values
	) {
		$this->values = $values;
	}

	public function __toString(): string {
		$result = [];
		foreach($this->values as $value) {
			$result[] = (string)$value;
		}
		return sprintf("%s = :[%s]", $this->typeName, implode(', ', $result));
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_definition_term',
			'type' => 'enumeration',
			'type_name' => $this->typeName,
			'values' => $this->values,
		];
	}

}