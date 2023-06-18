<?php

namespace Cast\Service\Expression;

use Cast\Model\Context\VariableValueScope;
use Cast\Service\Compiler\LanguageContext;
use Cast\Service\Expression\Behaviour\CatchExpressionBehaviour;
use Cast\Service\Expression\Behaviour\ConstantExpressionBehaviour;
use Cast\Service\Expression\Behaviour\ConstructorCallExpressionBehaviour;
use Cast\Service\Expression\Behaviour\FunctionBodyExpressionBehaviour;
use Cast\Service\Expression\Behaviour\FunctionCallExpressionBehaviour;
use Cast\Service\Expression\Behaviour\LoopExpressionBehaviour;
use Cast\Service\Expression\Behaviour\MatchExpressionBehaviour;
use Cast\Service\Expression\Behaviour\MethodCallExpressionBehaviour;
use Cast\Service\Expression\Behaviour\NativeCodeExpressionBehaviour;
use Cast\Service\Expression\Behaviour\PropertyAccessExpressionBehaviour;
use Cast\Service\Expression\Behaviour\RecordExpressionBehaviour;
use Cast\Service\Expression\Behaviour\ReturnExpressionBehaviour;
use Cast\Service\Expression\Behaviour\SequenceExpressionBehaviour;
use Cast\Service\Expression\Behaviour\ThrowExpressionBehaviour;
use Cast\Service\Expression\Behaviour\TupleExpressionBehaviour;
use Cast\Service\Expression\Behaviour\VariableAssignmentExpressionBehaviour;
use Cast\Service\Expression\Behaviour\VariableNameExpressionBehaviour;
use Cast\Service\NativeCode\NativeCodeBridge;
use Cast\Service\Registry\MethodFinder;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\ValueTypeResolver;

final readonly class ExpressionBehaviourCollectionFactory {
	public function __construct(
		private LanguageContext $languageContext
	) {}

	public function behaviourCollection(
		TypeAssistant             $typeAssistant,
		FunctionExecutor          $functionExecutor,
		VariableValueScope        $globalScope,
		AnalyserExpressionHandler $analyserExpressionHandler,
		MethodFinder              $methodFinder,
		ValueTypeResolver         $valueTypeResolver,
		NativeCodeBridge          $nativeCodeBridge,
	): ExpressionBehaviourCollection {
		$mcb = new MethodCallExpressionBehaviour(
			$functionExecutor,
			$methodFinder,
			$this->languageContext->unionTypeNormalizer,
			$analyserExpressionHandler
		);

		return new ExpressionBehaviourCollection(
			new CatchExpressionBehaviour(
				$typeAssistant,
				$valueTypeResolver,
				$this->languageContext->subtypeRelationChecker,
				$this->languageContext->unionTypeNormalizer,
				$analyserExpressionHandler
			),
			new ConstantExpressionBehaviour(
				$this->languageContext->subtypeRelationChecker,
				$valueTypeResolver,
			),
			new ConstructorCallExpressionBehaviour(
				$functionExecutor,
				$typeAssistant,
				$this->languageContext->unionTypeNormalizer,
				$this->languageContext->subtypeRelationChecker
			),
			new FunctionBodyExpressionBehaviour($this->languageContext->unionTypeNormalizer),
			new FunctionCallExpressionBehaviour(
				$functionExecutor,
				$this->languageContext->unionTypeNormalizer,
				$this->languageContext->subtypeRelationChecker,
				$mcb,
				$this->languageContext->tupleToRecordTypeConverter,
			),
			new LoopExpressionBehaviour($this->languageContext->unionTypeNormalizer),
			new MatchExpressionBehaviour(
				$this->languageContext->unionTypeNormalizer,
				$valueTypeResolver,
				$this->languageContext->subtypeRelationChecker,
			),
			$mcb,
			new NativeCodeExpressionBehaviour($nativeCodeBridge),
			new PropertyAccessExpressionBehaviour($this->languageContext->propertyTypeFinder),
			new RecordExpressionBehaviour($this->languageContext->unionTypeNormalizer),
			new ReturnExpressionBehaviour($this->languageContext->unionTypeNormalizer),
			new SequenceExpressionBehaviour($this->languageContext->unionTypeNormalizer),
			new ThrowExpressionBehaviour($this->languageContext->unionTypeNormalizer),
			new TupleExpressionBehaviour($this->languageContext->unionTypeNormalizer),
			new VariableAssignmentExpressionBehaviour($analyserExpressionHandler),
			new VariableNameExpressionBehaviour($globalScope),
		);
	}
}