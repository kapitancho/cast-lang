<?php

namespace Cast\Model\Runtime\Type;

final readonly class FunctionType implements Type {
	public function __construct(
		public Type               $parameterType,
		public FunctionReturnType $returnType,
	) {}

	public function __toString(): string {
		return sprintf("^%s => %s", $this->parameterType, $this->returnType);
	}

}