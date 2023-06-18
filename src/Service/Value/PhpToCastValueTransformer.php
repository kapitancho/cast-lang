<?php

namespace Cast\Service\Value;

use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Literal\RealLiteral;
use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\Value;

final readonly class PhpToCastValueTransformer {

	/** @throws InvalidPhpValue */
	public function fromPhpToCast(mixed $value): DictValue|ListValue|LiteralValue {
		return match(gettype($value)) {
			'string' => new LiteralValue(new StringLiteral($value)),
			'double' => new LiteralValue(new RealLiteral($value)),
			'integer' => new LiteralValue(new IntegerLiteral($value)),
			'boolean' => new LiteralValue(new BooleanLiteral($value)),
			'NULL' => new LiteralValue(new NullLiteral),
			'array' => array_is_list($value) ?
				new ListValue(... array_map(
					$this->fromPhpToCast(...), $value
				)) : new DictValue(array_map(
					$this->fromPhpToCast(...), $value
				)),
			'object' => new DictValue(array_map(
				$this->fromPhpToCast(...), (array)$value
			)),
			default => InvalidPhpValue::of($value)
		};
	}

	/** @throws InvalidValue */
	public function fromCastToPhp(Value $value): array|string|int|bool|float|null {
		return match($value::class) {
			LiteralValue::class => match($value->literal::class) {
				NullLiteral::class => null,
				default => $value->literal->value
			},
			DictValue::class, ListValue::class => array_map($this->fromCastToPhp(...), $value->items),
			default => InvalidValue::of($value)
		};
	}

}