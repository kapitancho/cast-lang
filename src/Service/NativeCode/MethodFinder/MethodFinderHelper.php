<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Expression\NativeCodeExpression;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;

trait MethodFinderHelper {
	private function getFunction(
		string    $nativeCodeExpressionId,
		Type      $returnType,
		ErrorType $errorType = new ErrorType,
		Type      $parameterType = new NullType
	): FunctionValue {
		return new FunctionValue(
			$parameterType,
			new FunctionReturnType($returnType, $errorType),
			new FunctionBodyExpression(
				new NativeCodeExpression(
					$nativeCodeExpressionId,
					$parameterType
				)
			)
		);
	}
}