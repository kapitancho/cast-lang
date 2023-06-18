<?php

namespace Cast\Service\Type;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use LogicException;

final class UnknownType extends LogicException {
	private function __construct(public TypeNameIdentifier $name) {
		parent::__construct();
	}
	public static function withName(TypeNameIdentifier $name): never {
		throw new self($name);
	}
}