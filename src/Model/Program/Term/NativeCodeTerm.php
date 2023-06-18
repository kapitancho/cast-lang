<?php

namespace Cast\Model\Program\Term;



final class NativeCodeTerm implements Term {
	public function __construct(
		public string $id,
	) {}

	public function __toString(): string {
		return sprintf("[native: %s]",
			$this->id
		);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'native',
			'id' => $this->id
		];
	}
}