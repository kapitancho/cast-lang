<?php

namespace Cast\Model\Program\Type;

use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;

final readonly class ArrayTypeTerm implements TypeTerm {
	public function __construct(
		public TypeTerm $itemType,
		public LengthRange    $range
	) {}

	public function __toString(): string {
		$type = "Array<$this->itemType, $this->range>";
		return str_replace(["<Any, ..>", "<Any, ", ", ..>"], ["", "<", ">"], $type);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'array',
			'item_type' => $this->itemType,
			'length_range' => $this->range
		];
	}

	public static function base(): self {
		return new self(new AnyTypeTerm, new LengthRange(0, PlusInfinity::value));
	}
}