<?php

namespace Cast\Service\Type;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\Type;
use Cast\Service\Transformer\TypeRegistry;
use Iterator;

final readonly class TypeAssistant {
	public function __construct(
		private TypeRegistry       $typeByNameFinder,
		private TypeChainGenerator $typeChainGenerator
	) {}

	public function followAliases(TypeNameIdentifier|Type $type): Type {
		if ($type instanceof TypeNameIdentifier) {
			$type = $this->typeByNameFinder->typeByName($type);
		}
		while($type instanceof AliasType) {
			$type = $type->aliasedType();
		}
		return $type;
	}

	public function toBasicType(TypeNameIdentifier|Type $type): Type {
		if ($type instanceof TypeNameIdentifier) {
			$type = $this->typeByNameFinder->typeByName($type);
		}
		while($type instanceof AliasType || $type instanceof SubtypeType) {
			$type = $type instanceof AliasType ? $type->aliasedType() : $type->baseType();
		}
		return $type;
	}

	public function typeByName(TypeNameIdentifier $name): Type {
		return $this->typeByNameFinder->typeByName($name);
	}

	public function closestTypeName(Type $type): TypeNameIdentifier {
		foreach($this->typeChainGenerator->generateNamedTypeChain($type) as $typeName) {
			return $typeName;
		}
		return new TypeNameIdentifier('Any');
	}

	/**
	 * @param Type $type
	 * @return Iterator<TypeNameIdentifier>
	 */
	public function allNamedTypes(Type $type): Iterator {
		yield from $this->typeChainGenerator->generateNamedTypeChain($type);
	}

}