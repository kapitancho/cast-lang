<?php

namespace Cast\Model\Program\Term;

use Cast\Model\Program\Constant\Constant;
use Cast\Model\Program\Constant\LiteralConstant;
use Cast\Model\Program\Literal\NullLiteral;

final readonly class ConstantTerm implements Term {
	public function __construct(
		public Constant $constant,
	) {}

	public function __toString(): string {
		return (string)$this->constant;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'constant',
			'constant' => $this->constant
		];
	}

	public static function emptyTerm(): self {
		static $null = null;
		return $null ??= new ConstantTerm(
			new LiteralConstant(new NullLiteral)
		);
	}

}