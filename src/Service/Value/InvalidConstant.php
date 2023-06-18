<?php

namespace Cast\Service\Value;

use Cast\Model\Program\Constant\Constant;
use LogicException;

final class InvalidConstant extends LogicException {

	private function __construct(public Constant $constant) {
		parent::__construct();
	}

	public static function of(Constant $constant): never {
		throw new self($constant);
	}
}