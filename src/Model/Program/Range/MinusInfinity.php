<?php

namespace Cast\Model\Program\Range;

use JsonSerializable;

enum MinusInfinity implements JsonSerializable {
	case value;

	public function jsonSerialize(): string {
		return '-infinity';
	}
}