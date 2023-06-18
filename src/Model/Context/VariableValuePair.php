<?php

namespace Cast\Model\Context;

use Cast\Model\Program\Identifier\VariableNameIdentifier;

final readonly class VariableValuePair {
	public function __construct(
		public VariableNameIdentifier $variableName,
		public TypedValue $typedValue
	) {}
}