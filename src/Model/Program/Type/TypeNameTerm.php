<?php

namespace Cast\Model\Program\Type;

use Cast\Model\Program\Identifier\TypeNameIdentifier;

final readonly class TypeNameTerm implements TypeTerm {
	public function __construct(public TypeNameIdentifier $typeName) {}

	public function __toString(): string {
		return $this->typeName;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'type_name',
			'type_name' => $this->typeName
		];
	}
}