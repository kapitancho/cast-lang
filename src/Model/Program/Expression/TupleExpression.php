<?php

namespace Cast\Model\Program\Expression;

final readonly class TupleExpression implements Expression {
	/** @var array<int, Expression>  */
	public array $values;

	public function __construct(
		Expression ... $values,
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
}