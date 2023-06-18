<?php

namespace Cast\Model\Runtime\Type;

final readonly class NothingType implements Type {
	public function __toString(): string {
		return "Nothing";
	}

}