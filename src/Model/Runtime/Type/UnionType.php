<?php

namespace Cast\Model\Runtime\Type;

final readonly class UnionType implements Type {
	/** @var non-empty-list<Type> $types */
	public array $types;

	public function __construct(
		Type ... $types
	) {
		$this->types = $types;
	}

	public function __toString(): string {
		$result = [];
		foreach($this->types as $value) {
			$result[] = (string)$value;
		}
		return sprintf("(%s)", implode("|", $result));
	}

}