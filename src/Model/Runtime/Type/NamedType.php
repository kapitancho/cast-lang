<?php

namespace Cast\Model\Runtime\Type;

use Cast\Model\Program\Identifier\TypeNameIdentifier;

interface NamedType {
	public function typeName(): TypeNameIdentifier;
}