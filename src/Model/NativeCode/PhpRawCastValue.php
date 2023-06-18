<?php

namespace Cast\Model\NativeCode;

use Cast\Model\Runtime\Value\Value;
use JsonSerializable;

final readonly class PhpRawCastValue implements JsonSerializable {
	public function __construct(
		public Value $value
	) {}

	public function jsonSerialize(): Value {
		return $this->value;
	}
}