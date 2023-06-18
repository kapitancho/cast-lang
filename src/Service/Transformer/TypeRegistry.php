<?php

namespace Cast\Service\Transformer;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Runtime\Type\Type;
use Cast\Service\Type\UnknownType;

interface TypeRegistry {
	/** @return Type[] */
	public function allTypes(): array;
	/** @throws UnknownType */
	public function typeByName(TypeNameIdentifier $name): Type;
}