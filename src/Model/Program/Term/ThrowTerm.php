<?php

namespace Cast\Model\Program\Term;



final readonly class ThrowTerm implements Term {
	public function __construct(
		public Term $thrownValue
	) {}

	public function __toString(): string {
		return sprintf("@ %s", $this->thrownValue);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'throw',
			'thrown_value' => $this->thrownValue
		];
	}
}
