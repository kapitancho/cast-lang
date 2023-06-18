<?php

namespace Cast\Model\Runtime\Type;

final readonly class BooleanType implements Type {
	public function __toString(): string {
		return "Boolean";
	}
}