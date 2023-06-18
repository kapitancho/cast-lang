<?php

namespace Cast\Model\Program\Type;

use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;

final readonly class EnumerationValueTypeTerm implements TypeTerm {
	public function __construct(
		public TypeNameIdentifier $enumTypeName,
		public EnumValueIdentifier $enumValue,
	) {}

	public function __toString(): string {
		return sprintf("%s[%s]", $this->enumTypeName, $this->enumValue);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'enumeration_value_type',
			'enumeration' => $this->enumTypeName,
			'value' => $this->enumValue,
		];
	}

}