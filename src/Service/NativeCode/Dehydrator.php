<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\Value;

interface Dehydrator {
	/** @throws DehydrationException */
	public function dehydrate(Value $value, string $hydrationPath): Value;
}