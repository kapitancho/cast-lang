<?php

namespace Cast\Model\Program\Type;

final readonly class BooleanTypeTerm implements TypeTerm {
	public function __toString(): string {
		return "Boolean";
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'boolean'
		];
	}
}