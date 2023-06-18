<?php

namespace Cast\Model\Program\Term;



final readonly class BinaryTerm implements Term {
	public function __construct(
		public BinaryOperation $operation,
		public Term            $firstExpression,
		public Term            $secondExpression,
	) {}

	public function __toString(): string {
		return sprintf("%s %s %s",
			$this->firstExpression,
			$this->operation->value,
			$this->secondExpression
		);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'binary',
			'operation' => $this->operation->value,
			'first_target' => $this->firstExpression,
			'second_target' => $this->secondExpression
		];
	}
}