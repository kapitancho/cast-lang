<?php

namespace Cast\Model\Program\Identifier;

use JsonSerializable;

final readonly class VariableNameIdentifier implements JsonSerializable {
	/** @throws IdentifierException */
	public function __construct(
		public string $identifier
	) {
		preg_match('/^(([a-z][a-zA-Z0-9]*)|#|#([a-zA-Z0-9_]+)|\$)$/', $identifier) ||
			IdentifierException::invalidVariableNameIdentifier($identifier);
	}

	public function __toString(): string {
		return $this->identifier;
	}

	public function jsonSerialize(): string {
		return $this->identifier;
	}
}