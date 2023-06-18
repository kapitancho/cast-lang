<?php

namespace Cast\Service\Builder;

use Cast\Service\Compiler\LanguageContext;
use Cast\Service\Type\IntersectionTypeNormalizer;
use Cast\Service\Type\PropertyTypeFinder;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TupleToRecordTypeConverter;
use Cast\Service\Type\TypeChainGenerator;
use Cast\Service\Type\UnionTypeNormalizer;
use Cast\Service\Type\ValueToTypeConverter;

final readonly class LanguageContextBuilder {
	public function buildContext(): LanguageContext {
		return new LanguageContext(
			new ValueToTypeConverter,
			$subtypeRelationChecker = new SubtypeRelationChecker,
			$intersectionTypeNormalizer = new IntersectionTypeNormalizer($subtypeRelationChecker),
			$unionTypeNormalizer = new UnionTypeNormalizer($subtypeRelationChecker),
			new PropertyTypeFinder(
				$intersectionTypeNormalizer, $unionTypeNormalizer
			),
			new TypeChainGenerator($unionTypeNormalizer),
			new TupleToRecordTypeConverter,
		);
	}

}