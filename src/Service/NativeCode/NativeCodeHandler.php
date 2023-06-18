<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Execution\ExecutionResultValueContext;
use Cast\Service\Execution\Flow\FlowShortcut;
use Cast\Service\Expression\SubExpressionExecutor;

interface NativeCodeHandler {
	/**
	 * @param Value $target
	 * @param Value $parameter
	 * @param VariableValueScope $globalScope
	 * @param SubExpressionExecutor $subExpressionExecutor
	 * @return ExecutionResultValueContext
	 * @throws FlowShortcut
	 */
	public function execute(
		Value                 $target,
		Value                 $parameter,
		VariableValueScope    $globalScope,
		SubExpressionExecutor $subExpressionExecutor,
		Expression            $expression,
		Type                  $targetType,
		Type                  $parameterType,
	): ExecutionResultValueContext;

}