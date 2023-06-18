<?php

namespace Cast\Service\Transformer;

use Cast\Model\Program\Constant\Constant;
use Cast\Model\Runtime\Value\Value;

interface ConstantToValueConverter {
	public function convertConstantToValue(Constant $constant): Value;
}