<?php

namespace Cast\Model\Program\Term;


use Cast\Model\Program\Type\TypeTerm;

final readonly class MatchTypePairTerm implements Term {
	public function __construct(
		public TypeTerm $matchedExpression,
		public Term           $valueExpression,
	) {}

	public function __toString(): string {
		return sprintf("%s : %s", $this->matchedExpression, $this->valueExpression);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'match_type_pair',
			'matched_expression' => $this->matchedExpression,
			'value_expression' => $this->valueExpression
		];
	}

}