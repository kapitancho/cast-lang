<?php

namespace Cast\Service\Type;

use Cast\Model\Program\Identifier\EnumValueIdentifier;
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
use Cast\Model\Runtime\Type\FunctionType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\MutableType;
use Cast\Model\Runtime\Type\NamedType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Type\UnionType;

final readonly class SubtypeRelationChecker {
	private UnionTypeNormalizer $unionTypeNormalizer;

	public function __construct() {
		$this->unionTypeNormalizer = new UnionTypeNormalizer($this);
	}

	public function isSubtypeFunctionType(FunctionType $subtype, FunctionType $ofType): bool {
		return
			$this->isSubtype($ofType->parameterType, $subtype->parameterType) &&
			$this->isSubtype($subtype->returnType->returnType, $ofType->returnType->returnType);
	}

	public function isSubtypeIntegerType(IntegerType $subtype, IntegerType $ofType): bool {
		return $subtype->range->isSubRangeOf($ofType->range);
	}

	public function isSubtypeRealType(RealType $subtype, RealType $ofType): bool {
		return $subtype->range->isSubRangeOf($ofType->range);
	}

	public function isSubtypeStringType(StringType $subtype, StringType $ofType): bool {
		return $subtype->range->isSubRangeOf($ofType->range);
	}

	public function isSubtypeEnumerationValueType(EnumerationValueType $subtype, EnumerationValueType $ofType): bool {
		return $subtype->enumType->typeName->identifier === $ofType->enumType->typeName->identifier &&
			$subtype->enumValue->identifier === $ofType->enumValue->identifier;
	}

	public function isSubtypeEnumerationType(EnumerationType $subtype, EnumerationType $ofType): bool {
		return $subtype->typeName->identifier === $ofType->typeName->identifier;
	}

	public function isSubtypeOfUnionType(Type $type, UnionType $ofType): bool {
		foreach($ofType->types as $ofTypeType) {
			if ($this->isSubtype($type, $ofTypeType)) {
				return true;
			}
		}
		return false;
	}

	public function isSubtypeOfIntersectionType(Type $type, IntersectionType $ofType): bool {
		foreach($ofType->types as $ofTypeType) {
			if (!$this->isSubtype($type, $ofTypeType)) {
				return false;
			}
		}
		return true;
	}

	public function isSubtypeUnionType(UnionType $subtype, UnionType $ofType): bool {
		foreach($subtype->types as $subtypeType) {
			if (!$this->isSubtypeOfUnionType($subtypeType, $ofType)) {
				return false;
			}
		}
		return true;
	}

	public function isSubtypeIntersectionType(IntersectionType $subtype, IntersectionType $ofType): bool {
		foreach($subtype->types as $subtypeType) {
			if ($this->isSubtypeOfIntersectionType($subtypeType, $ofType)) {
				return true;
			}
		}
		return false;
	}

	public function isSubtypeTupleType(TupleType $subtype, TupleType $ofType): bool {
		$subtypes = $subtype->types;
		if (count($subtypes) < count($ofType->types)) {
			return false;
		}
		foreach($ofType->types as $k => $ofTypeType) {
			if (!$this->isSubtype($subtypes[$k], $ofTypeType)) {
				return false;
			}
		}
		return true;
	}

	public function isSubtypeRecordType(RecordType $subtype, RecordType $ofType): bool {
		$subtypes = $subtype->types;
		foreach($ofType->types as $k => $ofTypeType) {
			if (!array_key_exists($k, $subtypes)) {
				return false;
			}
			if (!$this->isSubtype($subtypes[$k], $ofTypeType)) {
				return false;
			}
		}
		return true;
	}

	public function isSubtypeArrayType(ArrayType $subtype, ArrayType $ofType): bool {
		return $this->isSubtype($subtype->itemType, $ofType->itemType) &&
			$subtype->range->isSubRangeOf($ofType->range);
	}

	public function isSubtypeMapType(MapType $subtype, MapType $ofType): bool {
		return $this->isSubtype($subtype->itemType, $ofType->itemType) &&
			$subtype->range->isSubRangeOf($ofType->range);
	}

	public function isSubtypeMutableType(MutableType $subtype, MutableType $ofType): bool {
		return $this->isSubtype($subtype->refType, $ofType->refType) &&
			$this->isSubtype($ofType->refType, $subtype->refType);
	}

	public function isSubtypeTypeType(TypeType $subtype, TypeType $ofType): bool {
		return $this->isSubtype($subtype->refType, $ofType->refType);
	}

	public function isSubtypeSubtype(SubtypeType $subtype, SubtypeType $ofType): bool {
		if ($subtype->typeName()->identifier === $ofType->typeName()->identifier) {
			return true;
		}
		return $this->isSubtype($subtype->baseType(), $ofType);
	}

	/** @noinspection PhpParamsInspection */
	public function isSubtype(Type $subtype, Type $ofType): bool {
		return $this->isSubtypeInternal($subtype, $ofType);
	}

	/** @noinspection PhpParamsInspection */
	public function isSubtypeInternal(Type $subtype, Type $ofType): bool {
		if ((string)$subtype === (string)$ofType) { return true; }
		if (
			$subtype instanceof NamedType &&
			$ofType instanceof NamedType &&
			$subtype->typeName()->identifier === $ofType->typeName()->identifier
		) { return true; }

		if ($subtype instanceof AliasType) { return $this->isSubtype($subtype->aliasedType(), $ofType); }
		if ($ofType instanceof AliasType) { return $this->isSubtype($subtype, $ofType->aliasedType()); }

		$sc = $subtype::class;
		$oc = $ofType::class;

		if ($subtype instanceof TupleType && $ofType instanceof ArrayType) {
			return $this->isSubtypeArrayType(
				new ArrayType(
					$this->unionTypeNormalizer->normalize(... $subtype->types),
					new LengthRange($len = count($subtype->types), $len)
				),
				$ofType
			);
		}
		if ($subtype instanceof RecordType && $ofType instanceof MapType) {
			return $this->isSubtypeMapType(
				new MapType(
					$this->unionTypeNormalizer->normalize(... $subtype->types),
					new LengthRange($len = count($subtype->types), $len)
				),
				$ofType
			);
		}
		if ($subtype instanceof EnumerationType && $ofType instanceof StringType) {
			$min = min(array_map(static fn(EnumValueIdentifier $value): int =>
				strlen($value->identifier), $subtype->values) ?: [0]);
			$max = max(array_map(static fn(EnumValueIdentifier $value): int =>
				strlen($value->identifier), $subtype->values) ?: [0]);
			return $min >= $ofType->range->minLength && (
				$ofType->range->maxLength === PlusInfinity::value ||
				$max <= $ofType->range->maxLength
			);
		}

		if ($sc === $oc) {
			/** @psalm-suppress ArgumentTypeCoercion */
			return match($sc) {
				AnyType::class, NothingType::class, TrueType::class,
					FalseType::class, BooleanType::class, NullType::class => true,
				IntegerType::class => $this->isSubtypeIntegerType($subtype, $ofType),
				RealType::class => $this->isSubtypeRealType($subtype, $ofType),
				StringType::class => $this->isSubtypeStringType($subtype, $ofType),
				EnumerationValueType::class =>  $this->isSubtypeEnumerationValueType($subtype, $ofType),
				EnumerationType::class => $this->isSubtypeEnumerationType($subtype, $ofType),
				FunctionType::class => $this->isSubtypeFunctionType($subtype, $ofType),
				UnionType::class => $this->isSubtypeUnionType($subtype, $ofType),
				IntersectionType::class => $this->isSubtypeIntersectionType($subtype, $ofType),
				TupleType::class => $this->isSubtypeTupleType($subtype, $ofType),
				RecordType::class => $this->isSubtypeRecordType($subtype, $ofType),
				ArrayType::class => $this->isSubtypeArrayType($subtype, $ofType),
				MapType::class => $this->isSubtypeMapType($subtype, $ofType),
				MutableType::class => $this->isSubtypeMutableType($subtype, $ofType),
				TypeType::class => $this->isSubtypeTypeType($subtype, $ofType),
				default => match(true) {
					$sc instanceof SubtypeType => $this->isSubtypeSubtype($subtype, $ofType),
					default => false
				}
			};
		}

		if ($subtype instanceof EnumerationValueType && $ofType instanceof EnumerationType) {
			return $subtype->enumType->typeName->identifier === $ofType->typeName->identifier;
		}

		return match(true) {
			$ofType instanceof AnyType,
			$subtype instanceof NothingType
				=> true,

			$ofType instanceof BooleanType =>
				$subtype instanceof TrueType || $subtype instanceof FalseType ||
				($subtype instanceof UnionType &&
					$this->isSubtypeUnionType($subtype, new UnionType($ofType))),

			$subtype instanceof UnionType => $this->isSubtypeUnionType($subtype, new UnionType($ofType)),
			$ofType instanceof UnionType => $this->isSubtypeOfUnionType($subtype, $ofType),
			$subtype instanceof IntersectionType => $this->isSubtype($subtype, new IntersectionType($ofType)),
			$ofType instanceof IntersectionType => $this->isSubtypeOfIntersectionType($subtype, $ofType),

			$subtype instanceof IntegerType && $ofType instanceof RealType =>
				$this->isSubtypeIntegerOfReal($subtype, $ofType),

			$subtype instanceof SubtypeType && !($ofType instanceof SubtypeType) =>
				$this->isSubtype($subtype->baseType(), $ofType),

			default => false,
		};
	}

	public function isSubtypeIntegerOfReal(IntegerType $subtype, RealType $ofType): bool {
		return $this->isSubtype(
			new RealType(
				new RealRange(
					$subtype->range->minValue,
					$subtype->range->maxValue
				)
			),
			$ofType
		);
	}
}