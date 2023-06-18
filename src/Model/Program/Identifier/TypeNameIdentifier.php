<?php

namespace Cast\Model\Program\Identifier;

use JsonSerializable;

final readonly class TypeNameIdentifier implements JsonSerializable {
	/** @throws IdentifierException */
	public function __construct(
		public string $identifier
	) {
		preg_match('/^[A-Z][a-zA-Z0-9]*$/', $identifier) ||
			IdentifierException::invalidTypeNameIdentifier($identifier);
	}

	public function __toString(): string {
		return $this->identifier;
	}

	public function jsonSerialize(): string {
		return $this->identifier;
	}
}