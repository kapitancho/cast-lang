<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\Value;
use RuntimeException;

final class DehydrationException extends RuntimeException {
	public function __construct(
		public readonly Value $value,
		public readonly string $hydrationPath,
		public readonly string $errorMessage
	) {
		parent::__construct();
	}
}