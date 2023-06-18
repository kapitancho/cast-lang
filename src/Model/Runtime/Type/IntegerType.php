<?php

namespace Cast\Model\Runtime\Type;

use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;

final readonly class IntegerType implements Type {
	public function __construct(
		public IntegerRange $range
	) {}

	public function __toString(): string {
		$type = "Integer<$this->range>";
		return str_replace("<..>", "", $type);
	}

	public static function base(): self {
		return new self(new IntegerRange(MinusInfinity::value, PlusInfinity::value));
	}
}