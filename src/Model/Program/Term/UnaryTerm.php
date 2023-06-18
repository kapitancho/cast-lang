<?php

namespace Cast\Model\Program\Term;



final readonly class UnaryTerm implements Term {
	public function __construct(
		public UnaryOperation $operation,
		public Term           $expression,
	) {}

	public function __toString(): string {
		return sprintf("%s %s",
			$this->operation->value,
			$this->expression
		);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'unary',
			'operation' => $this->operation->value,
			'target' => $this->expression
		];
	}
}