<?php

namespace Cast\Service\Expression;

use Cast\Model\Program\Expression\CatchExpression;
use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\ConstructorCallExpression;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Expression\FunctionCallExpression;
use Cast\Model\Program\Expression\LoopExpression;
use Cast\Model\Program\Expression\MatchExpression;
use Cast\Model\Program\Expression\MethodCallExpression;
use Cast\Model\Program\Expression\NativeCodeExpression;
use Cast\Model\Program\Expression\PropertyAccessExpression;
use Cast\Model\Program\Expression\RecordExpression;
use Cast\Model\Program\Expression\ReturnExpression;
use Cast\Model\Program\Expression\SequenceExpression;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\ThrowExpression;
use Cast\Model\Program\Expression\TupleExpression;
use Cast\Model\Program\Expression\VariableAssignmentExpression;
use Cast\Model\Program\Expression\VariableNameExpression;
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

final class ExpressionBehaviourCollection {
	public function __construct(
		public CatchExpressionBehaviour              $catchExpressionBehaviour,
		public ConstantExpressionBehaviour           $constantExpressionBehaviour,
		public ConstructorCallExpressionBehaviour    $constructorCallExpressionBehaviour,
		public FunctionBodyExpressionBehaviour       $functionBodyExpressionBehaviour,
		public FunctionCallExpressionBehaviour       $functionCallExpressionBehaviour,
		public LoopExpressionBehaviour               $loopExpressionBehaviour,
		public MatchExpressionBehaviour              $matchExpressionBehaviour,
		public MethodCallExpressionBehaviour         $methodCallExpressionBehaviour,
		public NativeCodeExpressionBehaviour         $nativeCodeExpressionBehaviour,
		public PropertyAccessExpressionBehaviour     $propertyAccessExpressionBehaviour,
		public RecordExpressionBehaviour             $recordExpressionBehaviour,
		public ReturnExpressionBehaviour             $returnExpressionBehaviour,
		public SequenceExpressionBehaviour           $sequenceExpressionBehaviour,
		public ThrowExpressionBehaviour              $throwExpressionBehaviour,
		public TupleExpressionBehaviour              $tupleExpressionBehaviour,
		public VariableAssignmentExpressionBehaviour $variableAssignmentExpressionBehaviour,
		public VariableNameExpressionBehaviour       $variableNameExpressionBehaviour,
	) {}

	public function getAnalyser(Expression $expression): ExpressionAnalyser {
		return $this->getAnalyserExecutor($expression);
	}

	public function getExecutor(Expression $expression): ExpressionExecutor {
		return match(true) {
			$expression instanceof NativeCodeExpression => $this->nativeCodeExpressionBehaviour,
			default => $this->getAnalyserExecutor($expression)
		};
	}

	private function getAnalyserExecutor(Expression $expression): ExpressionAnalyser&ExpressionExecutor {
		return match(true) {
			$expression instanceof CatchExpression => $this->catchExpressionBehaviour,
			$expression instanceof ConstantExpression => $this->constantExpressionBehaviour,
			$expression instanceof ConstructorCallExpression => $this->constructorCallExpressionBehaviour,
			$expression instanceof FunctionBodyExpression => $this->functionBodyExpressionBehaviour,
			$expression instanceof FunctionCallExpression => $this->functionCallExpressionBehaviour,
			$expression instanceof LoopExpression => $this->loopExpressionBehaviour,
			$expression instanceof MatchExpression => $this->matchExpressionBehaviour,
			$expression instanceof MethodCallExpression => $this->methodCallExpressionBehaviour,
			$expression instanceof PropertyAccessExpression => $this->propertyAccessExpressionBehaviour,
			$expression instanceof RecordExpression => $this->recordExpressionBehaviour,
			$expression instanceof ReturnExpression => $this->returnExpressionBehaviour,
			$expression instanceof SequenceExpression => $this->sequenceExpressionBehaviour,
			$expression instanceof ThrowExpression => $this->throwExpressionBehaviour,
			$expression instanceof TupleExpression => $this->tupleExpressionBehaviour,
			$expression instanceof VariableAssignmentExpression => $this->variableAssignmentExpressionBehaviour,
			$expression instanceof VariableNameExpression => $this->variableNameExpressionBehaviour,
			default => die("Missing support for: " . $expression::class)
		};
	}

}