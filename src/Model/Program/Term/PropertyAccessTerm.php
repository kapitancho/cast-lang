<?php

namespace Cast\Model\Program\Term;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;

final readonly class PropertyAccessTerm implements Term {
	public function __construct(
		public VariableNameTerm|PropertyAccessTerm|SequenceTerm $target,
		public PropertyNameIdentifier                           $propertyName,
	) {}

	public function __toString(): string {
		return sprintf("%s.%s", $this->target, $this->propertyName);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'property_access',
			'target' => $this->target,
			'property_name' => $this->propertyName,
		];
	}
}