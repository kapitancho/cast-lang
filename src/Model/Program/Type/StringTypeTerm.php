<?php

namespace Cast\Model\Program\Type;

use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;

final readonly class StringTypeTerm implements TypeTerm {
	public function __construct(
		public LengthRange $range
	) {}

	public function __toString(): string {
		$type = "String<$this->range>";
		return str_replace("<..>", "", $type);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'string',
			'length_range' => $this->range
		];
	}

	public static function base(): self {
		return new self(new LengthRange(0, PlusInfinity::value));
	}
}