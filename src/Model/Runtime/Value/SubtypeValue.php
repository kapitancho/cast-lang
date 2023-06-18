<?php

namespace Cast\Model\Runtime\Value;

use Cast\Model\Program\Identifier\TypeNameIdentifier;

final readonly class SubtypeValue implements Value {
	public function __construct(
		public TypeNameIdentifier $typeName,
		public Value $baseValue,
	) {}

	public function __toString(): string {
		$param = (string)$this->baseValue;
		if (str_starts_with($param, '[') && str_ends_with($param, ']')) {
			return sprintf("%s%s", $this->typeName, $param);
		}
		if ($param === 'null') {
			$param = '';
		}
		return sprintf("%s(%s)", $this->typeName, $param);
	}

}