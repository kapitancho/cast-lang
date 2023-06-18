<?php

namespace Cast\Model\Program\Term;


use Cast\Model\Program\Identifier\VariableNameIdentifier;

final readonly class VariableNameTerm implements Term {
	public function __construct(
		public VariableNameIdentifier $variableName,
	) {}

	public function __toString(): string {
		return (string)$this->variableName;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'variable_name',
			'variable_name' => $this->variableName
		];
	}
}