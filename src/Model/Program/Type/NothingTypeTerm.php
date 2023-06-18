<?php

namespace Cast\Model\Program\Type;

final readonly class NothingTypeTerm implements TypeTerm {
	public function __toString(): string {
		return "Nothing";
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'nothing',
		];
	}
}