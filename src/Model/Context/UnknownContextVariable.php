<?php

namespace Cast\Model\Context;

use Cast\Model\Program\Identifier\VariableNameIdentifier;
use LogicException;

final class UnknownContextVariable extends LogicException {

	private function __construct(public VariableNameIdentifier $variableName) {
		parent::__construct();
	}

	public static function withName(VariableNameIdentifier $variableName): never {
		throw new self($variableName);
	}
}