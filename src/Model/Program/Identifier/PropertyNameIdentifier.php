<?php

namespace Cast\Model\Program\Identifier;

use JsonSerializable;

final readonly class PropertyNameIdentifier implements JsonSerializable {
	/** @throws IdentifierException */
	public function __construct(
		public string $identifier
	) {
		preg_match('/^(([a-zA-Z0-9_]+)|\d+)$/', $identifier) ||
			IdentifierException::invalidPropertyNameIdentifier($identifier);
	}

	public function __toString(): string {
		return $this->identifier;
	}

	public function jsonSerialize(): string {
		return $this->identifier;
	}
}