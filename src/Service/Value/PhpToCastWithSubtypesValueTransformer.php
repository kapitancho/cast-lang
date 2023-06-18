<?php

namespace Cast\Service\Value;

use Cast\Model\NativeCode\PhpEnumerationValue;
use Cast\Model\NativeCode\PhpRawCastValue;
use Cast\Model\NativeCode\PhpSubtypeValue;
use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Literal\RealLiteral;
use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\Value;

final readonly class PhpToCastWithSubtypesValueTransformer {

	/** @throws InvalidPhpValue */
	public function fromPhpToCast(mixed $value): Value {
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
			'object' => match(true) {
				$value instanceof PhpSubtypeValue =>
					new SubtypeValue(
						new TypeNameIdentifier($value->typeName),
						$this->fromPhpToCast($value->baseValue)
					),
				$value instanceof PhpEnumerationValue =>
					new EnumerationValue(
						new TypeNameIdentifier($value->typeName),
						new EnumValueIdentifier($value->enumValue)
					),
				$value instanceof PhpRawCastValue => $value->value,
				default => new DictValue(array_map(
						$this->fromPhpToCast(...), (array)$value
					))
			},
			default => InvalidPhpValue::of($value)
		};
	}

	/** @throws InvalidValue */
	public function fromCastToPhp(Value $value): array|string|int|bool|float|null|PhpSubtypeValue|PhpEnumerationValue|PhpRawCastValue {
		return match($value::class) {
			SubtypeValue::class => new PhpSubtypeValue(
				$value->typeName, $this->fromCastToPhp($value->baseValue)
			),
			EnumerationValue::class => new PhpEnumerationValue(
				$value->enumTypeName->identifier,
				$value->enumValue->identifier
			),
			LiteralValue::class => match($value->literal::class) {
				NullLiteral::class => null,
				default => $value->literal->value
			},
			DictValue::class, ListValue::class => array_map($this->fromCastToPhp(...), $value->items),
			default => new PhpRawCastValue($value)
			//default => InvalidValue::of($value)
		};
	}

}