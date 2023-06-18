<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\ValueTypeResolver;
use SplObjectStorage;

final class ContainerFactory {

	private SplObjectStorage $containerCache;

	public function __construct(
		private readonly TypeAssistant $typeAssistant,
		private readonly SubtypeRelationChecker $subtypeRelationChecker,
		private readonly CastRegistry $castRegistry,
		private readonly FunctionExecutor $functionExecutor,
		private readonly ValueTypeResolver $valueTypeResolver,
	) {
		$this->containerCache = new SplObjectStorage;
	}

	/** @throws ContainerException */
	private function initContainer(
		ListValue $configuration,
		SubExpressionExecutor $subExpressionExecutor,
		Value $containerValue
	): Container {
		return new LookupContainer(
			$this->typeAssistant,
			$this->subtypeRelationChecker,
			$this->castRegistry,
			$this->functionExecutor,
			$this->valueTypeResolver,
			$configuration,
			$subExpressionExecutor,
			$containerValue
		);
	}

	/** @throws ContainerException */
	public function getContainer(
		ListValue $configuration,
		SubExpressionExecutor $subExpressionExecutor,
		Value $containerValue
	): Container {
		$container = $this->containerCache[$configuration] ?? null;
		if (!$container) {
			$container = $this->initContainer(
				$configuration,
				$subExpressionExecutor,
				$containerValue
			);
			$this->containerCache[$configuration] = $container;
		}
		return $container;
	}

}