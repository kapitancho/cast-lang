<?php

namespace Cast\Service\Builder;

use Cast\Service\Compiler\LanguageContext;
use Cast\Service\Compiler\ProgramAnalyser;
use Cast\Service\Compiler\ProgramAnalyserFactory;
use Cast\Service\Compiler\ProgramContext;
use Cast\Service\Compiler\ProgramExecutor;
use Cast\Service\Expression\AnalyserExpressionHandler;
use Cast\Service\Expression\CodeExpressionAnalyser;
use Cast\Service\Expression\CodeExpressionExecutor;
use Cast\Service\Expression\ContextCodeExpressionAnalyser;
use Cast\Service\Expression\ContextCodeExpressionExecutor;
use Cast\Service\Expression\ExpressionBehaviourCollection;
use Cast\Service\Expression\ExpressionBehaviourCollectionFactory;
use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\NativeCode\ContainerFactory;
use Cast\Service\NativeCode\DehydratorFactory;
use Cast\Service\NativeCode\HydratorFactory;
use Cast\Service\NativeCode\MethodFinder\BuiltinMethodFinder;
use Cast\Service\NativeCode\NativeCodeBridge;
use Cast\Service\NativeCode\NativeCodeMethodProvider;
use Cast\Service\NativeCode\NestedHydrator;
use Cast\Service\Registry\CastBasedMethodFinder;
use Cast\Service\Registry\CompositeTypeMethodFinder;
use Cast\Service\Registry\NestedMethodFinder;
use Cast\Service\Type\ExpressionToTypeConverter;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\ValueUpCaster;
use Cast\Service\Value\PhpToCastValueTransformer;
use Cast\Service\Value\PhpToCastWithSubtypesValueTransformer;

final readonly class ProgramRuntimeBuilder {

	private ExpressionBehaviourCollection $behaviourCollection;
	private FunctionExecutor $functionExecutor;
	private AnalyserExpressionHandler $analyserExpressionHandler;
	private TypeAssistant $typeAssistant;

	public function __construct(
		private LanguageContext $languageContext,
		private ProgramContext $programContext
	) {
		$this->analyserExpressionHandler = new AnalyserExpressionHandler;
		$this->typeAssistant = new TypeAssistant(
			$this->programContext->typeByNameFinder,
			$this->languageContext->typeChainGenerator
		);
		$this->functionExecutor = new FunctionExecutor($this->typeAssistant);
		$this->behaviourCollection = $this->getBehaviourCollection();
	}

	private function getBehaviourCollection(): ExpressionBehaviourCollection {
		$expressionToTypeConverter = new ExpressionToTypeConverter;

		$methodFinder = new CompositeTypeMethodFinder(
			$this->typeAssistant,
			$this->languageContext->unionTypeNormalizer,
			new NestedMethodFinder(
				new BuiltinMethodFinder(
					$this->typeAssistant,
					$this->languageContext->unionTypeNormalizer,
					$this->languageContext->subtypeRelationChecker,
					$this->programContext->castRegistry,
					$this->languageContext->typeChainGenerator,
					$expressionToTypeConverter,
				),
				new CastBasedMethodFinder(
					$this->typeAssistant,
					$this->programContext->castRegistry,
					$this->languageContext->unionTypeNormalizer,
					$this->languageContext->subtypeRelationChecker,
					$this->languageContext->typeChainGenerator,
					$this->analyserExpressionHandler
				)
			)
		);

		$phpToCastWithSubtypesValueTransformer = new PhpToCastWithSubtypesValueTransformer;
		$valueUpcaster = new ValueUpCaster(
			$this->typeAssistant,
			$this->programContext->valueTypeResolver,
			$this->languageContext->subtypeRelationChecker,
		);

		$hydratorFactory = new HydratorFactory(
			$this->typeAssistant,
			$this->languageContext->subtypeRelationChecker,
			$this->programContext->castRegistry,
			$this->functionExecutor
		);

		$dehydratorFactory = new DehydratorFactory(
			$this->typeAssistant,
			$this->languageContext->subtypeRelationChecker,
			$this->programContext->castRegistry,
			$this->functionExecutor
		);

		$containerFactory = new ContainerFactory(
			$this->typeAssistant,
			$this->languageContext->subtypeRelationChecker,
			$this->programContext->castRegistry,
			$this->functionExecutor,
			$this->programContext->valueTypeResolver
		);

		$nativeCodeMethodProvider = new NativeCodeMethodProvider(
			$this->typeAssistant,
			$this->programContext->valueTypeResolver,
			$this->languageContext->subtypeRelationChecker,
			$this->languageContext->valueToTypeConverter,
			$phpToCastWithSubtypesValueTransformer,
			$this->programContext->castRegistry,
			$valueUpcaster,
			$this->functionExecutor,
			$this->analyserExpressionHandler,
			$hydratorFactory,
			$dehydratorFactory,
			$containerFactory
		);
		$nativeCodeBridge = new NativeCodeBridge($nativeCodeMethodProvider->getMethods());

		$behaviourCollectionFactory = new ExpressionBehaviourCollectionFactory($this->languageContext);
		return $behaviourCollectionFactory->behaviourCollection(
			$this->typeAssistant,
			$this->functionExecutor,
			$this->programContext->globalScope,
			$this->analyserExpressionHandler,
			$methodFinder,
			$this->programContext->valueTypeResolver,
			$nativeCodeBridge
		);
	}

	public function getProgramAnalyser(): ProgramAnalyser {
		$codeExpressionAnalyser = new ContextCodeExpressionAnalyser(
			new CodeExpressionAnalyser($this->behaviourCollection),
			$this->analyserExpressionHandler,
		);
		$programAnalyserFactory = new ProgramAnalyserFactory(
			$this->programContext->typeByNameFinder,
			$codeExpressionAnalyser,
			$this->languageContext->subtypeRelationChecker,
		);
		return $programAnalyserFactory->getAnalyser();
	}

	public function getProgramExecutor(): ProgramExecutor {
		$codeExpressionExecutor = new ContextCodeExpressionExecutor(
			new CodeExpressionExecutor($this->behaviourCollection),
		);
		return new ProgramExecutor(
			$this->functionExecutor,
			$codeExpressionExecutor
		);
	}

}