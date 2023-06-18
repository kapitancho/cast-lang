<?php

namespace Cast\Service\NativeCode;

use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;

final readonly class HydratorFactory {

	public function __construct(
		private TypeAssistant $typeAssistant,
		private SubtypeRelationChecker $subtypeRelationChecker,
		private CastRegistry $castRegistry,
		private FunctionExecutor $functionExecutor,
	) {}

	/** @throws HydrationException */
	public function getHydrator(SubExpressionExecutor $subExpressionExecutor): Hydrator {
		return new NestedHydrator(
			$this->typeAssistant,
			$this->subtypeRelationChecker,
			$this->castRegistry,
			$this->functionExecutor,
			$subExpressionExecutor
		);
	}

}