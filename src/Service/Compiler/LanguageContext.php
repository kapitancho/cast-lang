<?php

namespace Cast\Service\Compiler;

use Cast\Service\Type\IntersectionTypeNormalizer;
use Cast\Service\Type\PropertyTypeFinder;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TupleToRecordTypeConverter;
use Cast\Service\Type\TypeChainGenerator;
use Cast\Service\Type\UnionTypeNormalizer;
use Cast\Service\Type\ValueToTypeConverter;

final readonly class LanguageContext {
	public function __construct(
		public ValueToTypeConverter $valueToTypeConverter,
		public SubtypeRelationChecker $subtypeRelationChecker,
		public IntersectionTypeNormalizer $intersectionTypeNormalizer,
		public UnionTypeNormalizer $unionTypeNormalizer,
		public PropertyTypeFinder $propertyTypeFinder,
		public TypeChainGenerator $typeChainGenerator,
		public TupleToRecordTypeConverter $tupleToRecordTypeConverter,
	) {}
}