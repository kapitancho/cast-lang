<?php

namespace Cast\Model\Program\Expression;

use Cast\Model\Runtime\Type\Type;

final class NativeCodeExpression implements Expression {
	public function __construct(
		public string $id,
		public Type $parameterType,
	) {}

	public function __toString(): string {
		return sprintf("[native: %s]",
			$this->id
		);
	}
}