<?php

namespace Cast\Model\Runtime\Type;

final readonly class ErrorType {
	public function __construct(
		public bool $anyError = false,
		public Type $errorType = new NothingType,
	) {}

	public function __toString(): string {
		$pieces = [];
		$hasError = !($this->errorType instanceof NothingType);
		if ($this->anyError || $hasError) {
			$pieces[] = $this->anyError ? '@@' : '@';
			if ($hasError) {
				$pieces[] = (string)$this->errorType;
			}
		}
		return implode(' ', $pieces);
	}
}