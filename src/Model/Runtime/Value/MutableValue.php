<?php

namespace Cast\Model\Runtime\Value;

use Cast\Model\Runtime\Type\Type;

final class MutableValue implements Value {
	public function __construct(
		public Type $type,
		public Value $targetValue,
	) {}

	public function __toString(): string {
		return sprintf("Mutable[%s, %s]", $this->type, $this->targetValue);
	}

}