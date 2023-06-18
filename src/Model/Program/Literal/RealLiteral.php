<?php

namespace Cast\Model\Program\Literal;

use Cast\Model\Program\Node;

final readonly class RealLiteral implements Node {
	public function __construct(
		public float $value,
	) {}

	public function __toString(): string {
		return (string)$this->value;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'literal',
			'type' => 'real',
			'value' => $this->value
		];
	}
}