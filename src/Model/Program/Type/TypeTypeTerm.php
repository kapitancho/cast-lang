<?php

namespace Cast\Model\Program\Type;

final readonly class TypeTypeTerm implements TypeTerm {
	public function __construct(
		public TypeTerm $refType,
	) {}

	public function __toString(): string {
		$type = "Type<$this->refType>";
		return str_replace('<Any>', '', $type);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'type',
			'ref_type' => $this->refType
		];
	}
}