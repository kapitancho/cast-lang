<?php

namespace Cast\Model\Program\Range;

use RuntimeException;

final class InvalidLengthRange extends RuntimeException {
	public function __construct(
		public readonly int $minLength,
		public readonly int|PlusInfinity $maxLength
	) {
		parent::__construct();
	}
}