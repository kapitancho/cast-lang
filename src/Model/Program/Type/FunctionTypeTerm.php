<?php

namespace Cast\Model\Program\Type;

final readonly class FunctionTypeTerm implements TypeTerm {
	public function __construct(
		public TypeTerm $parameterType,
		public FunctionReturnTypeTerm $returnType,
	) {}

	public function __toString(): string {
		return sprintf("^%s => %s", $this->parameterType, $this->returnType);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'function',
			'parameter_type' => $this->parameterType,
			'return_type' => $this->returnType
		];
	}
}