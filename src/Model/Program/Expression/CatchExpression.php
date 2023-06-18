<?php

namespace Cast\Model\Program\Expression;

final readonly class CatchExpression implements Expression {
	public function __construct(
		public Expression $catchTarget,
		public bool $anyType = false
	) {}

	public function __toString(): string {
		return sprintf("%s %s", $this->anyType ? '@@' : '@', $this->catchTarget);
	}
}
