<?php

namespace Cast\Model\Program\Term\Expression;

use Cast\Model\Program\ProgramNode;
use Cast\Model\Program\Term\FunctionBodyTerm;
use Cast\Model\Program\Type\ErrorTypeTerm;
use Cast\Model\Program\Type\TypeNameTerm;

final readonly class TypeCastTerm implements ProgramNode {
	public function __construct(
		public TypeNameTerm $castFromType,
		public TypeNameTerm $castToType,
		public FunctionBodyTerm $functionBody,
		public ErrorTypeTerm $errorType = new ErrorTypeTerm,
	) {}

	public function __toString(): string {
		$errorType = (string)$this->errorType;
		if ($errorType !== '') {
			$errorType = ' ' . $errorType;
		}
		return sprintf("%s ==> %s%s :: %s",
			$this->castFromType, $this->castToType, $errorType, $this->functionBody);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'module_expression',
			'expression_type' => 'type_cast',
			'cast' => [
				'from_type' => $this->castFromType,
				'to_type' => $this->castToType,
				'function_body' => $this->functionBody,
				'any_error' => $this->errorType->anyError,
				'error_type' => $this->errorType->errorType,
			]
		];
	}
}