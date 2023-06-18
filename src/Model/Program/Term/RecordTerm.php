<?php

namespace Cast\Model\Program\Term;



final readonly class RecordTerm implements Term {
	/** @var array<string, Term>  */
	public array $values;

	public function __construct(
		Term ... $values,
	) {
		$this->values = $values;
	}

	public function asString(bool $multiline): string {
		$result = [];
		foreach($this->values as $key => $value) {
			$result[] = $multiline ?
				str_replace("\n", "\n" . "\t",
					sprintf("%s: %s", $key, $value)) :
				sprintf("%s: %s", $key, $value);
		}
		return $multiline ?
			sprintf("[\n\t%s\n]", implode(',' . "\n" . "\t", $result)):
			sprintf("[%s]", implode(", ", $result) ?: ':');
	}

	public function __toString(): string {
		$result = $this->asString(false);
		return mb_strlen($result) > 40 ? $this->asString(true) : $result;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'record',
			'value' => $this->values
		];
	}

}