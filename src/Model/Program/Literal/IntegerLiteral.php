<?php

namespace Cast\Model\Program\Literal;

use Cast\Model\Program\Node;

final readonly class IntegerLiteral implements Node {
	public function __construct(
		public int $value,
	) {}

	public function __toString(): string {
		return (string)$this->value;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'literal',
			'type' => 'integer',
			'value' => $this->value
		];
	}
}