<?php

namespace Cast\Service\Type;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use LogicException;

final class TypeException extends LogicException {

	private const PropertyNotFound = "Property %s not found";
	private const MethodNotFound = "Method %s not found";
	private const CannotResolveType = "Type %s cannot be resolved";

	public static function propertyNotFound(PropertyNameIdentifier $propertyName): never {
		throw new self(sprintf(self::PropertyNotFound, $propertyName->identifier));
	}

	public static function methodNotFound(PropertyNameIdentifier $propertyName): never {
		throw new self(sprintf(self::MethodNotFound, $propertyName->identifier));
	}

	public static function cannotResolveType(Type $type): never {
		throw new self(sprintf(self::CannotResolveType, $type));
	}

}