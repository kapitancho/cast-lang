<?php

namespace Cast\Model\Runtime;

use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Runtime\Type\ErrorType;

final readonly class TypeCast {
	public function __construct(
		public TypeNameIdentifier $castFromType,
		public TypeNameIdentifier $castToType,
		public FunctionBodyExpression $functionBody,
		public ErrorType $errorType = new ErrorType,
	) {}
}