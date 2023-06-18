<?php

namespace Cast\Model\Program\Type;

final readonly class ErrorTypeTerm {
	public function __construct(
		public bool $anyError = false,
		public TypeTerm $errorType = new NothingTypeTerm,
	) {}

	public function __toString(): string {
		$pieces = [];
		$hasError = !($this->errorType instanceof NothingTypeTerm);
		if ($this->anyError || $hasError) {
			$pieces[] = $this->anyError ? '@@' : '@';
			if ($hasError) {
				$pieces[] = (string)$this->errorType;
			}
		}
		return implode(' ', $pieces);
	}
}