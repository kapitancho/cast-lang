<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\EnumerationValueType;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Service\Registry\NoMethodAvailable;

/**
 * @template-implements ByTypeMethodFinder<EnumerationType>
 */
final readonly class EnumMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	/**
	 * @param Type $originalTargetType
	 * @param EnumerationType $targetType
	 * @param PropertyNameIdentifier $methodName
	 * @param Type $parameterType
	 * @return FunctionValue|NoMethodAvailable
	 */
	public function findMethodFor(
		Type                   $originalTargetType,
		Type                   $targetType,
		PropertyNameIdentifier $methodName,
		Type                   $parameterType
	): FunctionValue|NoMethodAvailable {
		if ($methodName->identifier === 'textValue') {
			$t = $originalTargetType;
			if ($t instanceof EnumerationType) {
				/*
				$min = min(array_map(static fn(EnumValueIdentifier $value): int =>
					strlen($value->identifier), $t->values) ?: [0]);
				$max = max(array_map(static fn(EnumValueIdentifier $value): int =>
					strlen($value->identifier), $t->values) ?: [0]);
				$a = count($t->values);
				return $this->getFunction("Enum::value", new ArrayType(
					new StringType(new LengthRange($min, $max)),
					new LengthRange($a, $a)
				));*/
				$min = min(array_map(static fn(EnumValueIdentifier $value): int =>
					strlen($value->identifier), $t->values) ?: [0]);
				$max = max(array_map(static fn(EnumValueIdentifier $value): int =>
					strlen($value->identifier), $t->values) ?: [0]);
				return $this->getFunction("Enum::textValue",
					new StringType(new LengthRange($min, $max)),
				);
			}
			if ($t instanceof EnumerationValueType) {
				$l = strlen($t->enumValue->identifier);
				return new FunctionValue(
					new NullType,
					new FunctionReturnType(new StringType(
						new LengthRange($l, $l)
					)),
					new FunctionBodyExpression(
						new ConstantExpression(
							new LiteralValue(
								new StringLiteral($t->enumValue->identifier),
							)
						)
					)
				);
			}
		}
		if ($methodName->identifier === 'values') {
			$l = max(array_map(static fn(EnumValueIdentifier $value): int =>
				strlen($value->identifier), $targetType->values) ?: [0]);
			$a = count($targetType->values);
			return $this->getFunction("Enum::values", new ArrayType(
				new StringType(new LengthRange($l, $l)),
				new LengthRange($a, $a)
			));
		}
		return NoMethodAvailable::value;
	}
}