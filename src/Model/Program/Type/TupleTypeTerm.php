<?php

namespace Cast\Model\Program\Type;

final readonly class TupleTypeTerm implements TypeTerm {
	/** @var array<int, TypeTerm>  */
	public array $types;

	public function __construct(
		TypeTerm ... $types,
	) {
		$this->types = $types;
	}

	public function propertyByIndex(int $index): TypeTerm|null {
		return $this->types[$index] ?? null;
	}

	public function __toString(): string {
		$result = [];
		foreach($this->types as $value) {
			$result[] = (string)$value;
		}
		return sprintf("[%s]", implode(", ", $result));
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'tuple',
			'types' => $this->types
		];
	}
}