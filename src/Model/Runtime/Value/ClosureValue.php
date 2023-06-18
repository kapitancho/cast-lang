<?php

namespace Cast\Model\Runtime\Value;

use Cast\Model\Context\VariableValueScope;

final readonly class ClosureValue implements Value {
	public function __construct(
		public VariableValueScope $globalScope,
		public FunctionValue      $function,
	) {}

	public function __toString(): string {
		return sprintf("%s%s", implode(', ',
			$this->globalScope->variables()), $this->function);
	}

}