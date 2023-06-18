<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use LogicException;

final class RegistryException extends LogicException {

	private const UnknownType = "Unknown type: %s";

	private function __construct(public readonly TypeNameIdentifier $typeName) {
		parent::__construct(
			sprintf(self::UnknownType, $this->typeName)
		);
	}

	public static function unknownType(TypeNameIdentifier $typeName): never {
		throw new self($typeName);
	}

}