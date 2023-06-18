<?php

namespace Cast\Model\Runtime\Type;

use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;

final readonly class StringType implements Type {
	public function __construct(
		public LengthRange $range
	) {}

	public function __toString(): string {
		$type = "String<$this->range>";
		return str_replace("<..>", "", $type);
	}

	public static function base(): self {
		return new self(new LengthRange(0, PlusInfinity::value));
	}
}