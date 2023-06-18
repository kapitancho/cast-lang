<?php

namespace Cast\Model\Program\Literal;

use Cast\Model\Program\Node;

final readonly class BooleanLiteral implements Node {
	public function __construct(
		public bool $value,
	) {}

	public function __toString(): string {
		return $this->value ? "true" : "false";
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'literal',
			'type' => 'boolean',
			'value' => $this->value
		];
	}

}