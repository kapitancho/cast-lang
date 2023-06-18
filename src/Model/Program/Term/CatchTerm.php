<?php

namespace Cast\Model\Program\Term;

final readonly class CatchTerm implements Term {
	public function __construct(
		public SequenceTerm $catchTarget,
		public bool         $anyType = false
	) {}

	public function __toString(): string {
		return sprintf("%s %s", $this->anyType ? '@@' : '@', $this->catchTarget);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'catch',
			'any_type' => $this->anyType,
			'target' => $this->catchTarget
		];
	}
}
