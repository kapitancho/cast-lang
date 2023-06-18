<?php

namespace Cast\Model\Program\Term;

final readonly class FunctionBodyTerm implements Term {
	public function __construct(
		public Term $body
	) {}

	public function __toString(): string {
		return (string)$this->body;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'function_body',
			'body' => $this->body
		];
	}

	public static function emptyBody(): self {
		return new self(ConstantTerm::emptyTerm());
	}
}