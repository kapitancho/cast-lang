<?php

namespace Cast\Service\NativeCode;

use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;

final readonly class DehydratorFactory {

	public function __construct(
		private CastRegistry $castRegistry,
		private FunctionExecutor $functionExecutor,
	) {}

	/** @throws HydrationException */
	public function getDehydrator(SubExpressionExecutor $subExpressionExecutor): Dehydrator {
		return new NestedDehydrator(
			$this->castRegistry,
			$this->functionExecutor,
			$subExpressionExecutor
		);
	}

}