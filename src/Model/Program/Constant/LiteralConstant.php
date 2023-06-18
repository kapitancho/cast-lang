<?php

namespace Cast\Model\Program\Constant;

use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Literal\RealLiteral;
use Cast\Model\Program\Literal\StringLiteral;

final readonly class LiteralConstant implements Constant {
	public function __construct(
		public BooleanLiteral|IntegerLiteral|RealLiteral|StringLiteral|NullLiteral $literal
	) {}

	public function __toString(): string {
		return (string)$this->literal;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'constant',
			'type' => 'literal',
			'literal' => $this->literal
		];
	}
}