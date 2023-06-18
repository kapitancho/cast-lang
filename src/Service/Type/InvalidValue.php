<?php

namespace Cast\Service\Type;

use Cast\Model\Runtime\Value\Value;
use LogicException;

final class InvalidValue extends LogicException {

	private function __construct(public Value $value) {
		parent::__construct();
	}

	public static function of(Value $value): never {
		throw new self($value);
	}
}