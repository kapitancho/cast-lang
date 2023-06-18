<?php

namespace Cast\Service\Execution;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Runtime\Value\Value;

final readonly class ExecutionResultValueContext {
	public function __construct(
		public Value $value,
		public VariableValueScope $globalScope
	) {}

	public function withValue(Value $value): self {
		return new self(
			$value,
			$this->globalScope
		);
	}

	public function withGlobalScope(VariableValueScope $globalScope): self {
		return new self(
			$this->value,
			$globalScope
		);
	}
}