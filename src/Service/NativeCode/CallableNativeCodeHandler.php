<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Expression\SubExpressionExecutor;
use Closure;

final readonly class CallableNativeCodeHandler implements NativeCodeHandler {

	/**
	 * param Closure(VariableScope): ExecutionResultContext $analyseFn
	 * @param Closure(Value, Value, VariableValueScope, SubExpressionExecutor, Expression, Type, Type): ExecutionResultValueContext $executeFn
	 */
	public function __construct(
		//private Closure $analyseFn,
		private Closure $executeFn
	) {}

	/**
	 * @param Value $target
	 * @param Value $parameter
	 * @param VariableValueScope $globalScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 */
	public function execute(
		Value                 $target,
		Value                 $parameter,
		VariableValueScope    $globalScope,
		SubExpressionExecutor $subExpressionExecutor,
		Expression            $expression,
		Type                  $targetType,
		Type                  $parameterType
	): ExecutionResultValueContext {
		return ($this->executeFn)(
			$target,
			$parameter,
			$globalScope,
			$subExpressionExecutor,
			$expression,
			$targetType,
			$parameterType
		);
	}

}