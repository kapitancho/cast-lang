<?php

namespace Cast\Model\Program\Type;

final readonly class MutableTypeTerm implements TypeTerm {
	public function __construct(
		public TypeTerm $refType,
	) {}

	public function __toString(): string {
		$type = "Mutable<$this->refType>";
		return str_replace('<Any>', '', $type);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'mutable',
			'ref_type' => $this->refType
		];
	}
}