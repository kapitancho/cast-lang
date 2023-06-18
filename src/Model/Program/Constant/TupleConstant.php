<?php

namespace Cast\Model\Program\Constant;

final readonly class TupleConstant implements Constant {
	/** @var array<int, Constant>  */
	public array $items;

	public function __construct(
		Constant ... $items
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

	public function jsonSerialize(): array {
		return [
			'node' => 'constant',
			'type' => 'tuple',
			'items' => $this->items
		];
	}
}