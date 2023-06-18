<?php

namespace Cast\Model\Program\Term\Expression;

use Cast\Model\Program\Constant\Constant;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Program\ProgramNode;

final readonly class ConstantAssignmentTerm implements ProgramNode {
	public function __construct(
		public VariableNameIdentifier $variableName,
		public Constant $constant,
	) {}

	public function __toString(): string {
		return sprintf("%s = %s", $this->variableName, $this->constant);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'module_expression',
			'expression_type' => 'constant_assignment',
			'variable_name' => $this->variableName,
			'value' => $this->constant
		];
	}
}