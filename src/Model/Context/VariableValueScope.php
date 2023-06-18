<?php

namespace Cast\Model\Context;

use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\Value;

final readonly class VariableValueScope {
	/** @param array<string, VariableValuePair> $scope */
	private function __construct(
		public array $scope
	) {}

	/** @return string[] */
	public function variables(): array {
		return array_keys($this->scope);
	}

	/** @throws UnknownContextVariable */
	public function findVariable(VariableNameIdentifier $variableName): VariableValuePair|UnknownVariable {
		return $this->scope[$variableName->identifier] ?? UnknownVariable::value;
	}

	/** @throws UnknownContextVariable */
	public function findValueOf(VariableNameIdentifier $variableName): Value|UnknownVariable {
		$value = $this->findVariable($variableName);
		return $value instanceof VariableValuePair ?
			$this->getVariable($variableName)->typedValue->value : $value;
	}

	/** @throws UnknownContextVariable */
	public function getVariable(VariableNameIdentifier $variableName): VariableValuePair {
		return $this->scope[$variableName->identifier] ??
			UnknownContextVariable::withName($variableName);
	}

	/** @throws UnknownContextVariable */
	public function typedValueOf(VariableNameIdentifier $variableName): TypedValue {
		return $this->getVariable($variableName)->typedValue;
	}

	/** @throws UnknownContextVariable */
	public function valueOf(VariableNameIdentifier $variableName): Value {
		return $this->typedValueOf($variableName)->value;
	}

	/** @throws UnknownContextVariable */
	public function typeOf(VariableNameIdentifier $variableName): Type {
		return $this->typedValueOf($variableName)->type;
	}

	public static function fromValues(VariableValuePair ...$values): self {
		$scope = [];
		foreach($values as $value) {
			$scope[$value->variableName->identifier] = $value;
		}
		return new self($scope);
	}

	public function withAddedValues(VariableValuePair ...$values): self {
		$scope = $this->scope;
		foreach($values as $value) {
			$scope[$value->variableName->identifier] = $value;
		}
		return new self($scope);
	}

	public static function empty(): self {
		return new self([]);
	}
}