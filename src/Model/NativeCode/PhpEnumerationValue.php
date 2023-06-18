<?php

namespace Cast\Model\NativeCode;

use JsonSerializable;

final readonly class PhpEnumerationValue implements JsonSerializable {
	public function __construct(
		public string $typeName,
		public string $enumValue
	) {}

	public function jsonSerialize(): string {
		return $this->enumValue;
	}
}