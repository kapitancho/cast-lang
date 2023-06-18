<?php

namespace Cast\Model\Program\Type;

use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;

final readonly class IntegerTypeTerm implements TypeTerm {
	public function __construct(
		public IntegerRange $range
	) {}

	public function __toString(): string {
		$type = "Integer<$this->range>";
		return str_replace("<..>", "", $type);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'integer',
			'range' => $this->range
		];
	}

	public static function base(): self {
		return new self(new IntegerRange(MinusInfinity::value, PlusInfinity::value));
	}
}