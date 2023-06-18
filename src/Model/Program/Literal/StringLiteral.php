<?php

namespace Cast\Model\Program\Literal;

use Cast\Model\Program\Node;

final readonly class StringLiteral implements Node {
	public function __construct(
		public string $value,
	) {}

	public function __toString(): string {
		return sprintf('"%s"', $this->value);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'literal',
			'type' => 'string',
			'value' => $this->value
		];
	}
}