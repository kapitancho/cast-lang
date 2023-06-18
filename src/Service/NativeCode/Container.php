<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\Value;

interface Container {
	/** @throws ContainerException */
	public function instanceOf(Type $targetType): Value;
}