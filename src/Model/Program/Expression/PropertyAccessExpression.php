<?php

namespace Cast\Model\Program\Expression;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;

final readonly class PropertyAccessExpression implements Expression {
	public function __construct(
		public Expression $target,
		public PropertyNameIdentifier $propertyName,
	) {}

	public function __toString(): string {
		return sprintf("%s.%s", $this->target, $this->propertyName);
	}
}