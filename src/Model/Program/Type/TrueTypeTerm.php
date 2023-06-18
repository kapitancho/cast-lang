<?php

namespace Cast\Model\Program\Type;

final readonly class TrueTypeTerm implements TypeTerm {
	public function __toString(): string {
		return "True";
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'true'
		];
	}
}