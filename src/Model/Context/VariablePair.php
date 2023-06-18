<?php

namespace Cast\Model\Context;

use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\Type;

final readonly class VariablePair {
	public function __construct(
		public VariableNameIdentifier $variableName,
		public Type                   $variableType,
	) {}
}