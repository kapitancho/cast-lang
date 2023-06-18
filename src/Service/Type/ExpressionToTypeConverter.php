<?php

namespace Cast\Service\Type;

use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Value\TypeValue;

final readonly class ExpressionToTypeConverter {

	/** @throws InvalidValue */
	public function convertRecordType(RecordType $recordType): RecordType {
		$result = [];
		foreach($recordType->types as $key => $type) {
			$result[$key] = $this->convertExpressionToType($type);
		}
		return new RecordType(... $result);
	}

	/** @throws InvalidValue */
	public function convertTupleType(TupleType $tupleType): TupleType {
		$result = [];
		foreach($tupleType->types as $type) {
			$result[] = $this->convertExpressionToType($type);
		}
		return new TupleType(... $result);
	}

	/** @throws InvalidTypeExpression */
	public function convertExpressionToType(Type $expression): Type {
		return match(true) {
			$expression instanceof TypeType => $expression->refType,
			$expression instanceof RecordType => $this->convertRecordType($expression),
			$expression instanceof TupleType => $this->convertTupleType($expression),
			default => throw InvalidTypeExpression::of($expression)
		};
	}

	public function tryConvertExpressionToType(Type $expression): TypeValue|null {
		try {
			return new TypeValue($this->convertExpressionToType($expression));
		} catch (InvalidTypeExpression) {
			return null;
		}
	}
}