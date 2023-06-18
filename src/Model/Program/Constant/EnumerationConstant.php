<?php

namespace Cast\Model\Program\Constant;

use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;

final readonly class EnumerationConstant implements Constant {
	public function __construct(
		public TypeNameIdentifier $enumTypeName,
		public EnumValueIdentifier $enumValue,
	) {}

	public function __toString(): string {
		return sprintf("%s.%s", $this->enumTypeName, $this->enumValue);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'constant',
			'type' => 'enumeration_value',
			'enumeration' => $this->enumTypeName,
			'value' => $this->enumValue,
		];
	}

}