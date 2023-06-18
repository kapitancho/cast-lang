<?php

namespace Cast\Model\Program\Expression;

use Cast\Model\Program\Identifier\VariableNameIdentifier;

final readonly class VariableAssignmentExpression implements Expression {
	public function __construct(
		public VariableNameIdentifier $variableName,
		public Expression $value,
	) {}

	public function __toString(): string {
		return sprintf("%s = %s", $this->variableName, $this->value);
	}
}