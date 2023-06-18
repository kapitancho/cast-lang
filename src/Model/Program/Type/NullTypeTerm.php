<?php

namespace Cast\Model\Program\Type;

final readonly class NullTypeTerm implements TypeTerm {
	public function __toString(): string {
		return "Null";
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'null'
		];
	}
}