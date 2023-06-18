<?php

namespace Cast\Model\Runtime\Value;

use Cast\Model\Runtime\Type\Type;

final readonly class TypeValue implements Value {
	public function __construct(
		public Type $type,
	) {}

	public function __toString(): string {
		return (string)$this->type;
	}

}