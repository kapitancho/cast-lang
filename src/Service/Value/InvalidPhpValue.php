<?php

namespace Cast\Service\Value;

use LogicException;

final class InvalidPhpValue extends LogicException {

	private function __construct(public mixed $value) {
		parent::__construct();
	}

	public static function of(mixed $value): never {
		throw new self($value);
	}
}