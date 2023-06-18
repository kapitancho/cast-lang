<?php

namespace Cast\Model\Program\Term;



final readonly class DefaultMatchTerm implements Term {
	public function __construct(
		public Term $valueExpression,
	) {}

	public function __toString(): string {
		return sprintf("~ : %s", $this->valueExpression);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'default_match_pair',
			'value_expression' => $this->valueExpression
		];
	}

}