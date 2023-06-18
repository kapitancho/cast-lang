<?php

namespace Cast\Model\Runtime\Value;

use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;

final readonly class EnumerationValue implements Value {
	public function __construct(
		public TypeNameIdentifier $enumTypeName,
		public EnumValueIdentifier $enumValue,
	) {}

	public function __toString(): string {
		return sprintf("%s.%s", $this->enumTypeName, $this->enumValue);
	}

}