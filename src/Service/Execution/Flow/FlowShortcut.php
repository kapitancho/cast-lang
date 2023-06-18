<?php

namespace Cast\Service\Execution\Flow;

use Cast\Model\Runtime\Value\Value;
use RuntimeException;

class FlowShortcut extends RuntimeException {
	public function __construct(public readonly Value $v) {
		parent::__construct();
	}
}
