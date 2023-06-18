<?php

namespace Cast\Model\Runtime\Type;

use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;

final readonly class EnumerationType implements Type, NamedType {
	/** @var array<string, EnumValueIdentifier> $values */
	public array $values;

	public function __construct(
		public TypeNameIdentifier $typeName,
		EnumValueIdentifier ... $values
	) {
		$this->values = $values;
	}

	public function __toString(): string {
		return $this->typeName;
	}

	public function typeName(): TypeNameIdentifier {
		return $this->typeName;
	}
}