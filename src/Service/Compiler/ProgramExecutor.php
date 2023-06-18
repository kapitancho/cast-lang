<?php

namespace Cast\Service\Compiler;

use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Execution\Flow\ThrowResult;
use Cast\Service\Expression\ContextCodeExpressionExecutor;
use Cast\Service\Expression\ExecutorException;
use Cast\Service\Expression\FunctionExecutor as FunctionExecutorAlias;
use Cast\Service\Value\InvalidValue;
use Cast\Service\Value\PhpToCastValueTransformer;
use Cast\Service\Value\PhpToCastWithSubtypesValueTransformer;

final readonly class ProgramExecutor {

	public function __construct(
		private FunctionExecutorAlias         $functionExecutor,
		private ContextCodeExpressionExecutor $codeExpressionExecutor
	) {}

	/**
	 * @param DictValue $args
	 * @throws ExecutorException|ThrowResult
	 */
	public function executeProgram(ProgramContext $programContext, VariableNameIdentifier $mainMethodName, Value $args): Value {
		$mainMethod = $programContext->globalScope->valueOf($mainMethodName);
		if ($mainMethod instanceof FunctionValue) {
			return $this->functionExecutor->executeFunction(
				$this->codeExpressionExecutor,
				$mainMethod,
				$args
			);
		}
		return new LiteralValue(new NullLiteral);
	}

}