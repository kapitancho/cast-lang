<?php

namespace Cast\Model\Context;

use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\Type;

final readonly class VariableScope {
	/** @param array<string, Type> $scope */
	private function __construct(
		public array $scope
	) {}

	/** @return string[] */
	public function variables(): array {
		return array_keys($this->scope);
	}

	public function typeOf(VariableNameIdentifier $variableName): Type {
		return $this->scope[$variableName->identifier] ?? new NothingType;
	}

	public static function fromVariablePairs(VariablePair ...$variables): self {
		$scope = [];
		foreach($variables as $variable) {
			$scope[$variable->variableName->identifier] = $variable->variableType;
		}
		return new self($scope);
	}

	public function withAddedVariablePairs(VariablePair ...$variables): self {
		$scope = $this->scope;
		foreach($variables as $variable) {
			$scope[$variable->variableName->identifier] = $variable->variableType;
		}
		return new self($scope);
	}

	public static function empty(): self {
		return new self([]);
	}
}