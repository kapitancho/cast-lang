<?php

namespace Cast\Model\Program\Term;


use Cast\Model\Program\Identifier\VariableNameIdentifier;

final readonly class VariableAssignmentTerm implements Term {
	public function __construct(
		public VariableNameIdentifier $variableName,
		public Term                   $value,
	) {}

	public function __toString(): string {
		return sprintf("%s = %s", $this->variableName, $this->value);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'variable_assignment',
			'variable_name' => $this->variableName,
			'value' => $this->value
		];
	}
}