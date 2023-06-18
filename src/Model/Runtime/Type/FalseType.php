<?php

namespace Cast\Model\Runtime\Type;

final readonly class FalseType implements Type {
	public function __toString(): string {
		return "False";
	}

}