<?php

namespace Cast\Model\Runtime\Value;

final readonly class ListValue implements Value {
	/** @var array<int, Value>  */
	public array $items;

	public function __construct(
		Value ... $items
	) {
		$this->items = $items;
	}

	public function asString(bool $multiline): string {
		$result = [];
		foreach($this->items as $value) {
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