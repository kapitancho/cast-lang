<?php

namespace Cast\Model\Program\Constant;

use Cast\Model\Program\Type\TypeTerm;

final readonly class TypeConstant implements Constant {
	public function __construct(
		public TypeTerm $type,
	) {}

	public function __toString(): string {
		return (string)$this->type;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'constant',
			'type' => 'type',
			'type_value' => $this->type,
		];
	}

}