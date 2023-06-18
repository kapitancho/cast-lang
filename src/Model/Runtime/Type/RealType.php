<?php

namespace Cast\Model\Runtime\Type;

use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;

final readonly class RealType implements Type {
	public function __construct(
		public RealRange $range
	) {}

	public function __toString(): string {
		$type = "Real<$this->range>";
		return str_replace("<..>", "", $type);
	}

	public static function base(): self {
		return new self(new RealRange(MinusInfinity::value, PlusInfinity::value));
	}
}