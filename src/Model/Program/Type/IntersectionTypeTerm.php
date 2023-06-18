<?php

namespace Cast\Model\Program\Type;

final readonly class IntersectionTypeTerm implements TypeTerm {
	/** @var TypeTerm[] $types */
	public array $types;

	public function __construct(
		TypeTerm ... $types
	) {
		$this->types = $types;
	}

	public function __toString(): string {
		$result = [];
		foreach($this->types as $value) {
			$result[] = (string)$value;
		}
		return sprintf("(%s)", implode("&", $result));
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'intersection',
			'types' => $this->types
		];
	}
}