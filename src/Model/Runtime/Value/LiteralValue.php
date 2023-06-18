<?php

namespace Cast\Model\Runtime\Value;

use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Literal\RealLiteral;
use Cast\Model\Program\Literal\StringLiteral;

final readonly class LiteralValue implements Value {
	public function __construct(
		public BooleanLiteral|IntegerLiteral|RealLiteral|StringLiteral|NullLiteral $literal
	) {}

	public function __toString(): string {
		return (string)$this->literal;
	}

}