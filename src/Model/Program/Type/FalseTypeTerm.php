<?php

namespace Cast\Model\Program\Type;

final readonly class FalseTypeTerm implements TypeTerm {
	public function __toString(): string {
		return "False";
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'false'
		];
	}
}