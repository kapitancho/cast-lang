<?php

namespace Cast\Model\Program\Expression;

final readonly class ReturnExpression implements Expression {
	public function __construct(
		public Expression $returnedValue
	) {}

	public function __toString(): string {
		return sprintf("=> %s", $this->returnedValue);
	}
}
