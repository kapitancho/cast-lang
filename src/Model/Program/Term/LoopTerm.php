<?php

namespace Cast\Model\Program\Term;


final readonly class LoopTerm implements Term {
	public function __construct(
		public Term               $checkExpression,
		public LoopExpressionType $loopExpressionType,
		public SequenceTerm       $loopExpression,
	) {}

	public function __toString(): string {
		return sprintf("(%s) %s %s", $this->checkExpression, $this->loopExpressionType->sign(), $this->loopExpression);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'loop',
			'check_expression' => $this->checkExpression,
			'loop_expression_type' => $this->loopExpressionType->value,
			'loop_expression' => $this->loopExpression
		];
	}

}