<?php

namespace Cast\Service\Type;

use Cast\Model\Program\Type\TypeTerm;
use LogicException;

final class InvalidTypeTerm extends LogicException {
	private function __construct(public TypeTerm $typeTerm) {
		parent::__construct();
	}

	public static function of(TypeTerm $typeTerm): never {
		throw new self($typeTerm);
	}
}