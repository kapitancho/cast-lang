<?php

namespace Cast\Model\Runtime\Type;

final readonly class TrueType implements Type {
	public function __toString(): string {
		return "True";
	}

}