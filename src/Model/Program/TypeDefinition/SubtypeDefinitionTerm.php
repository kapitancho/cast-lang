<?php

namespace Cast\Model\Program\TypeDefinition;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Node;
use Cast\Model\Program\Term\FunctionBodyTerm;
use Cast\Model\Program\Type\ErrorTypeTerm;
use Cast\Model\Program\Type\NothingTypeTerm;
use Cast\Model\Program\Type\TypeTerm;

final readonly class SubtypeDefinitionTerm implements Node/*implements ResolvedType, ProgramNode*/ {
	public function __construct(
		public TypeNameIdentifier $typeName,
		public TypeTerm $baseType,
		public FunctionBodyTerm $functionBody,
		public ErrorTypeTerm $errorType = new ErrorTypeTerm(false, new NothingTypeTerm),
	) {}

	public function __toString(): string {
		$body = sprintf(" :: %s", $this->functionBody);
		if ($body === ' :: {null}') {
			$body = '';
		}
		if ($body === ' :: null') {
			$body = '';
		}
		$errorType = (string)$this->errorType;
		if ($errorType !== '') {
			$errorType = ' ' . $errorType;
		}
		return sprintf("%s <: %s%s%s",
			$this->typeName, $this->baseType, $errorType, $body);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_definition_term',
			'type' => 'subtype',
			'type_name' => $this->typeName,
			'any_error' => $this->errorType->anyError,
			'error_type' => $this->errorType->errorType,
			'base_type' => $this->baseType,
			'function_body' => $this->functionBody
		];
	}
}