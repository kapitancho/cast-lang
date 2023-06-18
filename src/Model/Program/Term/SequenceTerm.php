<?php

namespace Cast\Model\Program\Term;

final readonly class SequenceTerm implements Term {
	/** @var array<Term> $terms */
	public array $terms;
	public function __construct(Term ... $terms) {
		$this->terms = array_values($terms) ?: [ConstantTerm::emptyTerm()];
	}

	public function last(): Term {
		return $this->terms[count($this->terms) - 1];
	}

	public function __toString(): string {
		$result = [];
		foreach($this->terms as $term) {
			$result[] = str_replace("\n", "\n" . "\t", $term);
		}
		return count($result) > 1 || strlen($result[0]) > 30 ?
			sprintf("{\n\t%s\n}", implode(";" . "\n" . "\t", $result)) :
			sprintf("{%s}", $result[0]);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'sequence',
			'sequence' => $this->terms
		];
	}
}