<?php

namespace Cast\Service\Execution;

use Cast\Model\Context\VariableScope;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\Type;

final readonly class ExecutionResultContext {
	public function __construct(
		public Type          $expressionType,
		public Type          $returnType,
		public ErrorType     $errorType,
		public VariableScope $variableScope,
		public mixed         $contextData = null
	) {}

	public function withExpressionType(Type $expressionType): self {
		return new self(
			$expressionType,
			$this->returnType,
			$this->errorType,
			$this->variableScope,
			$this->contextData
		);
	}

	public function withReturnType(Type $returnType): self {
		return new self(
			$this->expressionType,
			$returnType,
			$this->errorType,
			$this->variableScope,
			$this->contextData
		);
	}

	public function withErrorType(ErrorType $errorType): self {
		return new self(
			$this->expressionType,
			$this->returnType,
			$errorType,
			$this->variableScope,
			$this->contextData
		);
	}

	public function withVariableScope(VariableScope $variableScope): self {
		return new self(
			$this->expressionType,
			$this->returnType,
			$this->errorType,
			$variableScope,
			$this->contextData
		);
	}

	public function withContextData(mixed $contextData): self {
		return new self(
			$this->expressionType,
			$this->returnType,
			$this->errorType,
			$this->variableScope,
			$contextData
		);
	}
}