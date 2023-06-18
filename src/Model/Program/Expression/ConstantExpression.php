<?php

namespace Cast\Model\Program\Expression;

use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\Value;

final readonly class ConstantExpression implements Expression {
	public function __construct(
		public Value $constant,
	) {}

	public function __toString(): string {
		return (string)$this->constant;
	}

	public static function emptyExpression(): self {
		static $null = null;
		return $null ??= new ConstantExpression(
			new LiteralValue(new NullLiteral)
		);
	}

}