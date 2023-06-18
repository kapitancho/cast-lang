<?php

namespace Cast\Service\Compiler;

use Cast\Model\Context\VariableValueScope;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Transformer\TypeRegistry;
use Cast\Service\Type\ValueTypeResolver;

final readonly class ProgramContext {
	public function __construct(
		public TypeRegistry       $typeByNameFinder,
		public ValueTypeResolver  $valueTypeResolver,
		public VariableValueScope $globalScope,
		public CastRegistry       $castRegistry
	) {}
}