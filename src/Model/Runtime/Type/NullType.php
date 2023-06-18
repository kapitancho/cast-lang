<?php

namespace Cast\Model\Runtime\Type;

final readonly class NullType implements Type {
	public function __toString(): string {
		return "Null";
	}

}