<?php

namespace Cast\Model\Runtime\Type;

final readonly class MutableType implements Type {
	public function __construct(
		public Type $refType,
	) {}

	public function __toString(): string {
		$type = "Mutable<$this->refType>";
		return str_replace('<Any>', '', $type);
	}

}