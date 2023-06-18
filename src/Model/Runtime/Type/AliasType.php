<?php

namespace Cast\Model\Runtime\Type;

interface AliasType extends Type, NamedType {
	public function aliasedType(): Type;
}