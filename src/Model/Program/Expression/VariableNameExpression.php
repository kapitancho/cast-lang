<?php

namespace Cast\Model\Program\Expression;

use Cast\Model\Program\Identifier\VariableNameIdentifier;

final readonly class VariableNameExpression implements Expression {
	public function __construct(
		public VariableNameIdentifier $variableName,
	) {}

	public function __toString(): string {
		return (string)$this->variableName;
	}
}