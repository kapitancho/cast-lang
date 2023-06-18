<?php

namespace Cast\Model\Program\Type;

use Cast\Model\Program\Node;

final readonly class FunctionReturnTypeTerm implements Node {
	public function __construct(
		public TypeTerm $returnType,
		public ErrorTypeTerm $errorType = new ErrorTypeTerm(false, new NothingTypeTerm),
	) {}

	public function __toString(): string {
		$pieces = [(string)$this->returnType];
		$errorType = (string)$this->errorType;
		if ($errorType !== '') {
			$pieces[] = $errorType;
		}
		return implode(' ', $pieces);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'function_return_type_term',
			'return_type' => $this->returnType,
			'any_error' => $this->errorType->anyError,
			'error_type' => $this->errorType->errorType
		];
	}
}