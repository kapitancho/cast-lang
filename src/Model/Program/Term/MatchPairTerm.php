<?php

namespace Cast\Model\Program\Term;



final readonly class MatchPairTerm implements Term {
	public function __construct(
		public Term $matchedExpression,
		public Term $valueExpression,
	) {}

	public function __toString(): string {
		return sprintf("%s : %s", $this->matchedExpression, $this->valueExpression);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'match_pair',
			'matched_expression' => $this->matchedExpression,
			'value_expression' => $this->valueExpression
		];
	}

}