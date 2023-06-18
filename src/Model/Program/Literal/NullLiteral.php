<?php

namespace Cast\Model\Program\Literal;

use Cast\Model\Program\Node;

final readonly class NullLiteral implements Node {
	public function __toString(): string {
		return "null";
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'literal',
			'type' => 'null',
		];
	}

}