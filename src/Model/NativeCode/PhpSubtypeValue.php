<?php

namespace Cast\Model\NativeCode;

use JsonSerializable;

final readonly class PhpSubtypeValue implements JsonSerializable {
	public function __construct(
		public string $typeName,
		public array|string|int|bool|float|null|PhpSubtypeValue|PhpEnumerationValue|PhpRawCastValue $baseValue
	) {}

	public function jsonSerialize(): array|string|int|bool|float|null|PhpSubtypeValue|PhpEnumerationValue|PhpRawCastValue {
		return $this->baseValue;
	}
}