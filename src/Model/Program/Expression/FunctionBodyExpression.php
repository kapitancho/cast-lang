<?php

namespace Cast\Model\Program\Expression;

final readonly class FunctionBodyExpression implements Expression {
	public function __construct(
		public Expression $body
	) {}

	public function __toString(): string {
		return (string)$this->body;
	}

	public static function emptyBody(): self {
		return new self(ConstantExpression::emptyExpression());
	}
}