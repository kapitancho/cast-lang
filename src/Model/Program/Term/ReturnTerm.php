<?php

namespace Cast\Model\Program\Term;



final readonly class ReturnTerm implements Term {
	public function __construct(
		public Term $returnedValue
	) {}

	public function __toString(): string {
		return sprintf("=> %s", $this->returnedValue);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'return',
			'returned_value' => $this->returnedValue
		];
	}
}
