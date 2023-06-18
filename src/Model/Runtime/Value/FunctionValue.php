<?php

namespace Cast\Model\Runtime\Value;

use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\Type;

final readonly class FunctionValue implements Value {
	public function __construct(
		public Type               $parameterType,
		public FunctionReturnType $returnType,
		public FunctionBodyExpression $functionBody,
	) {}

	public function __toString(): string {
		return sprintf("^%s => %s :: %s",
			$this->parameterType,
			$this->returnType,
			$this->functionBody
		);
	}

}