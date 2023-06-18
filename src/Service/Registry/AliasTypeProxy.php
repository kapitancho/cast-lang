<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Type\TypeTerm;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\Type;
use Cast\Service\Transformer\TermToTypeTransformer;

final readonly class AliasTypeProxy implements AliasType {
	public function __construct(
		private TermToTypeTransformer   $termToTypeTransformer,
		private TypeNameIdentifier $typeName,
		private TypeTerm $aliasedType,
	) {}

	public function __toString(): string {
		return sprintf("%s",  $this->typeName);
	}

	public function aliasedType(): Type {
		return $this->termToTypeTransformer->termToType($this->aliasedType);
	}

	public function typeName(): TypeNameIdentifier {
		return $this->typeName;
	}
}