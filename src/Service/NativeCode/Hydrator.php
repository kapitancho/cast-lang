<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\Value;

interface Hydrator {
	/** @throws HydrationException */
	public function hydrate(Value $value, Type $targetType, string $hydrationPath): Value;
}