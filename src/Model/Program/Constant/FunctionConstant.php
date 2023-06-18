<?php

namespace Cast\Model\Program\Constant;

use Cast\Model\Program\Term\FunctionBodyTerm;
use Cast\Model\Program\Type\FunctionReturnTypeTerm;
use Cast\Model\Program\Type\TypeTerm;

final readonly class FunctionConstant implements Constant {
	public function __construct(
		public TypeTerm     $parameterType,
		public FunctionReturnTypeTerm $returnType,
		public FunctionBodyTerm $functionBody,
	) {}

	public function __toString(): string {
		return sprintf("^%s => %s :: %s",
			$this->parameterType,
			$this->returnType,
			$this->functionBody
		);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'constant',
			'type' => 'function',
			'value' => [
				'parameter_type' => $this->parameterType,
				'return_type' => $this->returnType,
				'function_body' => $this->functionBody
			]
		];
	}

}