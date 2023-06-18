<?php

namespace Cast\Model\Context;

use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\Value;

final readonly class TypedValue {
	public function __construct(
		public Type                   $type,
		public Value                  $value,
	) {}
}