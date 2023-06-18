<?php

namespace Cast\Service\Value;

use Cast\Model\Program\Constant\Constant;
use Cast\Model\Program\Constant\EnumerationConstant;
use Cast\Model\Program\Constant\FunctionConstant;
use Cast\Model\Program\Constant\LiteralConstant;
use Cast\Model\Program\Constant\RecordConstant;
use Cast\Model\Program\Constant\TupleConstant;
use Cast\Model\Program\Constant\TypeConstant;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Transformer\ConstantToValueConverter;
use Cast\Service\Transformer\TermToExpressionConverter;
use Cast\Service\Transformer\TermToTypeTransformer;

final readonly class ProgramConstantToValueConverter implements ConstantToValueConverter {

	public function __construct(
		private TermToExpressionConverter $termToExpressionConverter,
		private TermToTypeTransformer $termToTypeTransformer
	) {}

	public function convertConstantToValue(Constant $constant): Value {
		return match($constant::class) {
			EnumerationConstant::class => new EnumerationValue(
				$constant->enumTypeName,
				$constant->enumValue,
			),
			FunctionConstant::class => new FunctionValue(
				$this->termToTypeTransformer->termToType($constant->parameterType),
				$this->termToTypeTransformer->functionReturnTermToType($constant->returnType),
				new FunctionBodyExpression(
					$this->termToExpressionConverter->termToExpression($constant->functionBody->body)
				)
			),
			LiteralConstant::class => new LiteralValue($constant->literal),
			RecordConstant::class => new DictValue(array_map(
				$this->convertConstantToValue(...),
				$constant->items
			)),
			TupleConstant::class => new ListValue(... array_map(
				$this->convertConstantToValue(...),
				$constant->items
			)),
			TypeConstant::class => new TypeValue(
				$this->termToTypeTransformer->termToType($constant->type)
			),
			default => InvalidConstant::of($constant)
		};
	}

}