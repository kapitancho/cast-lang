<?php

namespace Cast\Model\Program\Type;

final readonly class AnyTypeTerm implements TypeTerm {
	public function __toString(): string {
		return "Any";
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'any'
		];
	}
}