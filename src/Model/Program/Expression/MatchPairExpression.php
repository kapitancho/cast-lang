<?php

namespace Cast\Model\Program\Expression;

final readonly class MatchPairExpression implements Expression {
	public function __construct(
		public Expression $matchedExpression,
		public Expression $valueExpression,
	) {}

	public function __toString(): string {
		return sprintf("%s : %s", $this->matchedExpression, $this->valueExpression);
	}
}