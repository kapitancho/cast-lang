<?php

namespace Cast\Service\Type;

use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Model\Runtime\Value\Value;

final readonly class ValueToTypeConverter {

	/** @throws InvalidValue */
	public function convertDictValue(DictValue $dictValue): RecordType {
		$result = [];
		foreach($dictValue->items as $key => $item) {
			$result[$key] = $this->convertValueToType($item);
		}
		return new RecordType(... $result);
	}

	/** @throws InvalidValue */
	public function convertListValue(ListValue $dictValue): TupleType {
		$result = [];
		foreach($dictValue->items as $item) {
			$result[] = $this->convertValueToType($item);
		}
		return new TupleType(... $result);
	}

	/** @throws InvalidValue */
	public function convertValueToType(Value $value): Type {
		return match(true) {
			$value instanceof TypeValue => $value->type,
			$value instanceof DictValue => $this->convertDictValue($value),
			$value instanceof ListValue => $this->convertListValue($value),
			default => throw InvalidValue::of($value)
		};
	}

	public function tryConvertValueToType(Value $value): Type|null {
		try {
			return $this->convertValueToType($value);
		} catch (InvalidValue) {
			return null;
		}
	}
}