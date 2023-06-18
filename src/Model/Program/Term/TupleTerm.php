<?php

namespace Cast\Model\Program\Term;



final readonly class TupleTerm implements Term {
	/** @var array<int, Term>  */
	public array $values;

	public function __construct(
		Term ... $values,
	) {
		$this->values = $values;
	}

	public function asString(bool $multiline): string {
		$result = [];
		foreach($this->values as $value) {
			$result[] = $multiline ?
				str_replace("\n", "\n" . "\t", $value) :
				(string)$value;
		}
		return $multiline ?
			sprintf("[\n\t%s\n]", implode(',' . "\n" . "\t", $result)):
			sprintf("[%s]", implode(", ", $result));
	}

	public function __toString(): string {
		$result = $this->asString(false);
		return mb_strlen($result) > 40 ? $this->asString(true) : $result;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'tuple',
			'value' => $this->values
		];
	}

}