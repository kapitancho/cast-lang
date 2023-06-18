<?php

namespace Cast\Model\Program\Constant;

final readonly class RecordConstant implements Constant {
	/** @var array<string, Constant>  */
	public array $items;

	public function __construct(
		Constant ... $items
	) {
		$this->items = $items;
	}

	public function asString(bool $multiline): string {
		$result = [];
		foreach($this->items as $key => $value) {
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
			'node' => 'constant',
			'type' => 'record',
			'items' => $this->items
		];
	}
}