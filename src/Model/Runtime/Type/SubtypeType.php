<?php

namespace Cast\Model\Runtime\Type;

use Cast\Model\Program\Expression\FunctionBodyExpression;

interface SubtypeType extends Type, NamedType {
	public function baseType(): Type;
	public function functionBody(): FunctionBodyExpression;
	public function errorType(): ErrorType;
}