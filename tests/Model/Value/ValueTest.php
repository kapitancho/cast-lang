<?php

namespace Cast\Test\Model\Value;

use Cast\Model\Context\TypedValue;
use Cast\Model\Context\VariableValuePair;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Expression\VariableNameExpression;
use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Literal\RealLiteral;
use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Value\ClosureValue;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\MutableValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Program\Builder\FromArraySourceBuilder;
use PHPUnit\Framework\TestCase;

final class ValueTest extends TestCase {
	private Value $value;

	protected function setUp(): void {
		parent::setUp();

		$this->value = new DictValue([
			'mutable' => new MutableValue(
				new AnyType,
				new LiteralValue(new BooleanLiteral(true))
			),
			'boolean' => new LiteralValue(new BooleanLiteral(true)),
			'real'    => new LiteralValue(new RealLiteral(3.14)),
			'integer' => new LiteralValue(new IntegerLiteral(42)),
			'string'  => new LiteralValue(new StringLiteral("Hello")),
			'null'    => new LiteralValue(new NullLiteral),
			'type'    => new TypeValue(new AnyType),
			'others' => new ListValue(
				new EnumerationValue(
					new TypeNameIdentifier("Suit"),
					new EnumValueIdentifier("Spade"),
				),
				new FunctionValue(
					new AnyType,
					new FunctionReturnType(
						StringType::base(),
						new ErrorType(true, new NothingType)
					),
					new FunctionBodyExpression(
						new ConstantExpression(
							new LiteralValue(new NullLiteral)
						)
					)
				),
				new SubtypeValue(
					new TypeNameIdentifier("BaseType"),
					new LiteralValue(new IntegerLiteral(42))
				),
				new ClosureValue(
					VariableValueScope::fromValues(
						new VariableValuePair(
							new VariableNameIdentifier('x'),
							new TypedValue(
								StringType::base(),
								new LiteralValue(new StringLiteral("Test"))
							)
						)
					),
					new FunctionValue(
						new AnyType,
						new FunctionReturnType(
							StringType::base(),
							new ErrorType(true, new NothingType)
						),
						new FunctionBodyExpression(
							new VariableNameExpression(
								new VariableNameIdentifier("x")
							)
						)
					),
				)
			),
		]);
	}

	public function testToString(): void {
		self::assertEquals(
str_replace("\r\n", "\n", '[
	mutable: Mutable[Any, true],
	boolean: true,
	real: 3.14,
	integer: 42,
	string: "Hello",
	null: null,
	type: Any,
	others: [
		Suit.Spade,
		^Any => String @@ :: null,
		BaseType(42),
		x^Any => String @@ :: x
	]
]'),
			(string)$this->value
		);
	}

}