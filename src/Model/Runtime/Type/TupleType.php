<?php

namespace Cast\Model\Runtime\Type;

final readonly class TupleType implements Type {
	/** @var array<int, Type>  */
	public array $types;

	public function __construct(
		Type ... $types,
	) {
		$this->types = $types;
	}

	public function propertyByIndex(int $index): Type|null {
		return $this->types[$index] ?? null;
	}

	public function __toString(): string {
		$result = [];
		foreach($this->types as $value) {
			$result[] = (string)$value;
		}
		return sprintf("[%s]", implode(", ", $result));
	}

}