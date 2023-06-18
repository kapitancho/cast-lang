<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\Value;
use RuntimeException;

final class ContainerException extends RuntimeException {
	public function __construct(
		public readonly Type $targetType,
		public readonly string $errorMessage,
	) {
		parent::__construct();
	}
}