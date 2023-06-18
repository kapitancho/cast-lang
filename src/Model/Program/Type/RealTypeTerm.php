<?php

namespace Cast\Model\Program\Type;

use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;

final readonly class RealTypeTerm implements TypeTerm {
	public function __construct(
		public RealRange $range
	) {}

	public function __toString(): string {
		$type = "Real<$this->range>";
		return str_replace("<..>", "", $type);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'real',
			'range' => $this->range
		];
	}

	public static function base(): self {
		return new self(new RealRange(MinusInfinity::value, PlusInfinity::value));
	}
}