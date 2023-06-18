<?php

namespace Cast\Model\Program\Expression;

use Cast\Model\Program\Term\LoopExpressionType;

final readonly class LoopExpression implements Expression {
	public function __construct(
		public Expression $checkExpression,
		public LoopExpressionType $loopExpressionType,
		public Expression $loopExpression,
	) {}

	public function __toString(): string {
		return sprintf("(%s) %s %s",
			$this->checkExpression, $this->loopExpressionType->sign(), $this->loopExpression);
	}
}