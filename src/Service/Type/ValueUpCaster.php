<?php

namespace Cast\Service\Type;

use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Expression\ExecutorException;

final readonly class ValueUpCaster {

	public function __construct(
		private TypeAssistant $typeAssistant,
		private ValueTypeResolver $valueTypeFinder,
		private SubtypeRelationChecker $subtypeRelationChecker
	) {}

	private function cannotUpcast(Value $value, Type $valueType, Type $targetType): never {
		throw new ExecutorException(sprintf(
			"Cannot upcast %s of type %s to type %s",
			$value, $valueType, $targetType
		));
	}

	public function upcastSubtypeConstant(SubtypeValue $value, Type $targetType): Value {
		if ($targetType instanceof SubtypeType && $targetType->typeName() === $value->typeName) {
			return $value;
		}
		return $this->upcastValue($value->baseValue, $targetType);
	}

	public function upcastListValue(ListValue $value, ArrayType|TupleType|IntersectionType|UnionType $targetType): ListValue {
		if ($targetType instanceof UnionType) {
			foreach($targetType->types as $subtype) {
				if ($subtype instanceof ArrayType || $subtype instanceof TupleType || $subtype instanceof IntersectionType) {
					return $this->upcastListValue($value, $subtype);
				}
			}
		}
		$cnt = count($value->items);
		$values = [];
		for($i = 0; $i < $cnt; $i++) {
			$values[] = $this->upcastValue($value->items[$i],
				$targetType instanceof ArrayType ? $targetType->itemType : $targetType->types[$i]);
		}
		return new ListValue(... $values);
	}

	public function findRecordTypeInIntersection(string $key, IntersectionType $type): Type|null {
		foreach($type->types as $candidateType) {
			$candidateType = $this->typeAssistant->followAliases($candidateType);
			if ($candidateType instanceof RecordType && array_key_exists($key, $candidateType->types)) {
				return $candidateType->types[$key];
			}
		}
		return null;
	}

	public function upcastDictValue(DictValue $value, MapType|RecordType|IntersectionType|UnionType $targetType): DictValue {
		if ($targetType instanceof UnionType) {
			foreach($targetType->types as $subtype) {
				if ($subtype instanceof MapType || $subtype instanceof RecordType || $subtype instanceof IntersectionType) {
					return $this->upcastDictValue($value, $subtype);
				}
			}
		}
		$values = [];
		foreach(array_keys($value->items) as $key) {
			$values[$key] = $this->upcastValue($value->items[$key],
				match($targetType::class) {
					MapType::class => $targetType->itemType,
					RecordType::class => $targetType->types[$key],
					IntersectionType::class => $this->findRecordTypeInIntersection($key, $targetType) ??
						$targetType
				});
		}
		return new DictValue($values);
	}

	public function upcastValue(Value $value, Type $targetType): Value {
		$valueType = $this->valueTypeFinder->findTypeOf($value);
		if (!$this->subtypeRelationChecker->isSubtype($valueType, $targetType)) {
			$this->cannotUpcast($value, $valueType, $targetType);
		}
		$targetType = $this->typeAssistant->followAliases($targetType);
		/** @noinspection PhpParamsInspection */
		return match($value::class) {
			ListValue::class => $this->upcastListValue($value, $targetType),
			DictValue::class => $this->upcastDictValue($value, $targetType),
			EnumerationValue::class, FunctionValue::class, LiteralValue::class, TypeValue::class => $value,
			default => match(true) {
				$value instanceof SubtypeValue => $this->upcastSubtypeConstant($value, $targetType),
			}
		};
	}
}