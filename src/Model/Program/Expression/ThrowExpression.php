<?php

namespace Cast\Model\Program\Expression;

final readonly class ThrowExpression implements Expression {
	public function __construct(
		public Expression $thrownValue
	) {}

	public function __toString(): string {
		return sprintf("@ %s", $this->thrownValue);
	}
}
