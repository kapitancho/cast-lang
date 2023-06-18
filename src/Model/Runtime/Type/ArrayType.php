<?php

namespace Cast\Model\Runtime\Type;

use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;

final readonly class ArrayType implements Type {
	public function __construct(
		public Type        $itemType,
		public LengthRange $range
	) {}

	public function __toString(): string {
		$type = "Array<$this->itemType, $this->range>";
		return str_replace(["<Any, ..>", "<Any, ", ", ..>"], ["", "<", ">"], $type);
	}

	public static function base(): self {
		return new self(new AnyType, new LengthRange(0, PlusInfinity::value));
	}
}