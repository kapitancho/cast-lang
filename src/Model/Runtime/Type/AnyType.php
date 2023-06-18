<?php

namespace Cast\Model\Runtime\Type;

final readonly class AnyType implements Type {
	public function __toString(): string {
		return "Any";
	}
}