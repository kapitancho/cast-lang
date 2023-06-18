<?php

namespace Cast\Service\Type;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\UnionType;

final readonly class PropertyTypeFinder {

	public function __construct(
		private IntersectionTypeNormalizer $intersectionTypeNormalizer,
		private UnionTypeNormalizer $unionTypeNormalizer,
	) {}

	/** @throws TypeException */
	private function propertyNotFound(PropertyNameIdentifier $propertyName): never {
		TypeException::propertyNotFound($propertyName);
	}

	/** @throws TypeException */
	private function findTupleTypeProperty(TupleType $tupleType, PropertyNameIdentifier $propertyName): Type {
		return is_numeric($propertyName->identifier) && ($prop = $tupleType->propertyByIndex((int)$propertyName->identifier)) ?
			$prop : $this->propertyNotFound($propertyName);
	}

	/** @throws TypeException */
	private function findRecordTypeProperty(RecordType $recordType, PropertyNameIdentifier $propertyName): Type {
		return ($prop = $recordType->propertyByKey($propertyName->identifier)) ? $prop : $this->propertyNotFound($propertyName);
	}

	private function findUnionTypeProperty(UnionType $unionType, PropertyNameIdentifier $propertyName): Type {
		$propertyTypes = [];
		foreach($unionType->types as $type) {
			$propertyTypes[] = $this->findPropertyType($type, $propertyName);
		}
		return $this->unionTypeNormalizer->normalize(... $propertyTypes);
	}

	private function findIntersectionTypeProperty(IntersectionType $intersectionType, PropertyNameIdentifier $propertyName): Type {
		$propertyTypes = [];
		$e = null;
		foreach($intersectionType->types as $type) {
			try {
				$propertyTypes[] = $this->findPropertyType($type, $propertyName);
			} catch (TypeException $e) {}
		}
		if (!$e || count($propertyTypes) > 0) {
			return ($this->intersectionTypeNormalizer)(... $propertyTypes);
		}
		throw $e;
	}

	/** @throws TypeException */
	public function findPropertyType(
		Type                   $type,
		PropertyNameIdentifier $propertyName
	): Type {
		return match($type::class) {
			RecordType::class => $this->findRecordTypeProperty($type, $propertyName),
			TupleType::class => $this->findTupleTypeProperty($type, $propertyName),
			UnionType::class => $this->findUnionTypeProperty($type, $propertyName),
			IntersectionType::class => $this->findIntersectionTypeProperty($type, $propertyName),
			default => match(true) {
				$type instanceof SubtypeType => $this->findPropertyType($type->baseType(), $propertyName),
				$type instanceof AliasType => $this->findPropertyType($type->aliasedType(), $propertyName),
				default => $this->propertyNotFound($propertyName)
			}
		};
	}

}