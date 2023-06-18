<?php
/** @noinspection PhpNonStrictObjectEqualityInspection */
/** @noinspection TypeUnsafeComparisonInspection */

namespace Cast\Service\Type;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\EnumerationValueType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\NamedType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Iterator;

final readonly class TypeChainGenerator {

	public function __construct(
		private UnionTypeNormalizer $unionTypeNormalizer
	) {}

	private function getTypeChainForStringType(StringType $stringType): Iterator {
		if ($stringType != $base = StringType::base()) {
			yield $base;
		}
	}

	private function getTypeChainForRealType(RealType $realType): Iterator {
		if ($realType != $base = RealType::base()) {
			yield $base;
		}
	}

	private function getTypeChainForIntegerType(IntegerType $integerType): Iterator {
		if ($integerType != $base = IntegerType::base()) {
			yield $base;
			yield new RealType(new RealRange(
				$integerType->range->minValue,
				$integerType->range->maxValue
			));
		}
		yield RealType::base();
	}

	private function getTypeChainForIntersectionType(IntersectionType $intersectionType): Iterator {
		foreach($intersectionType->types as $type) {
			yield from $this->getTypeChain($type);
		}
	}

	private function getTypeChainForEnumerationValueType(EnumerationValueType $enumerationValueType): Iterator {
		yield from $this->getTypeChain($enumerationValueType->enumType);
	}

	private function getTypeChainForArrayType(ArrayType $arrayType): Iterator {
		if ($arrayType != $base = ArrayType::base()) {
			yield new ArrayType($arrayType->itemType,
				new LengthRange(0, PlusInfinity::value));
			yield $base;
		}
	}

	private function getTypeChainForMapType(MapType $mapType): Iterator {
		if ($mapType != $base = MapType::base()) {
			yield new MapType($mapType->itemType,
				new LengthRange(0, PlusInfinity::value));
			yield $base;
		}
	}

	/**
	 * @param Type $type
	 * @return Iterator<Type>
	 */
	private function getTypeChain(Type $type): Iterator {
		yield $type;
		yield from match($type::class) {
			TrueType::class, FalseType::class => $this->getTypeChain(new BooleanType),
			StringType::class => $this->getTypeChainForStringType($type),
			RealType::class => $this->getTypeChainForRealType($type),
			IntegerType::class => $this->getTypeChainForIntegerType($type),
			EnumerationValueType::class => $this->getTypeChainForEnumerationValueType($type),
			IntersectionType::class => $this->getTypeChainForIntersectionType($type),
			ArrayType::class => $this->getTypeChainForArrayType($type),
			MapType::class => $this->getTypeChainForMapType($type),
			TupleType::class => $this->getTypeChain(
				new ArrayType(
					$this->unionTypeNormalizer->normalize(...$type->types),
					new LengthRange($cnt = count($type->types), $cnt)
				)
			),
			RecordType::class => $this->getTypeChain(
				new MapType(
					$this->unionTypeNormalizer->normalize(...$type->types),
					new LengthRange($cnt = count($type->types), $cnt)
				)
			),
			default => match(true) {
				$type instanceof AliasType => $this->getTypeChain($type->aliasedType()),
				$type instanceof SubtypeType => $this->getTypeChain($type->baseType()),
				$type instanceof EnumerationType => $this->getTypeChain(StringType::base()),
				default => []
			}
		};
	}

	/**
	 * @param Type $type
	 * @return Iterator<Type>
	 */
	public function generateTypeChain(Type $type): Iterator {
		yield from $this->getTypeChain($type);
		yield new AnyType;
	}

	/**
	 * @param Type $type
	 * @return Iterator<TypeNameIdentifier>
	 */
	public function generateNamedTypeChain(Type $type): Iterator {
		foreach($this->generateTypeChain($type) as $typeCandidate) {
			if ($typeCandidate instanceof NamedType) {
				yield $typeCandidate->typeName();
			} else {
				$str = (string)$typeCandidate;
				if (in_array($str, [
					'Integer', 'String', 'Boolean', 'True', 'False', 'Real', 'Null', 'Array', 'Map', 'Any'
				], true)) {
					yield new TypeNameIdentifier($str);
				}
			}
		}
	}

}