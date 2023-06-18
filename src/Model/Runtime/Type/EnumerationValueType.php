<?php

namespace Cast\Model\Runtime\Type;

use Cast\Model\Program\Identifier\EnumValueIdentifier;

final readonly class EnumerationValueType implements Type {
	public function __construct(
		public EnumerationType     $enumType,
		public EnumValueIdentifier $enumValue,
	) {}

	public function __toString(): string {
		return sprintf("%s[%s]", $this->enumType, $this->enumValue);
	}

}