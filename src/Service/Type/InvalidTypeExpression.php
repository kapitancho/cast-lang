<?php

namespace Cast\Service\Type;

use Cast\Model\Runtime\Type\Type;
use LogicException;

final class InvalidTypeExpression extends LogicException {

	private function __construct(public Type $expression) {
		parent::__construct();
	}

	public static function of(Type $expression): never {
		throw new self($expression);
	}
}