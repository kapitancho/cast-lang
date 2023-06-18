<?php

namespace Cast\Test\Model\Constant;

use Cast\Model\Program\Constant\Constant;
use Cast\Model\Program\Constant\EnumerationConstant;
use Cast\Model\Program\Constant\FunctionConstant;
use Cast\Model\Program\Constant\LiteralConstant;
use Cast\Model\Program\Constant\RecordConstant;
use Cast\Model\Program\Constant\TupleConstant;
use Cast\Model\Program\Constant\TypeConstant;
use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Literal\RealLiteral;
use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Program\Term\ConstantTerm;
use Cast\Model\Program\Term\FunctionBodyTerm;
use Cast\Model\Program\Type\AnyTypeTerm;
use Cast\Model\Program\Type\ErrorTypeTerm;
use Cast\Model\Program\Type\FunctionReturnTypeTerm;
use Cast\Model\Program\Type\NothingTypeTerm;
use Cast\Model\Program\Type\StringTypeTerm;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Service\Program\Builder\FromArraySourceBuilder;
use PHPUnit\Framework\TestCase;

final class ConstantTest extends TestCase {
	private Constant $constant;

	protected function setUp(): void {
		parent::setUp();

		$this->constant = new RecordConstant(... [
			'boolean' => new LiteralConstant(new BooleanLiteral(true)),
			'real'    => new LiteralConstant(new RealLiteral(3.14)),
			'integer' => new LiteralConstant(new IntegerLiteral(42)),
			'string'  => new LiteralConstant(new StringLiteral("Hello")),
			'null'    => new LiteralConstant(new NullLiteral),
			'type'    => new TypeConstant(new AnyTypeTerm),
			'others' => new TupleConstant(
				new EnumerationConstant(
					new TypeNameIdentifier("Suit"),
					new EnumValueIdentifier("Spade"),
				),
				new FunctionConstant(
					new AnyTypeTerm,
					new FunctionReturnTypeTerm(
						StringTypeTerm::base(),
						new ErrorTypeTerm(true, new NothingTypeTerm)
					),
					new FunctionBodyTerm(
						new ConstantTerm(
							new LiteralConstant(new NullLiteral)
						)
					)
				),
			),
			'long' => new TupleConstant(
				new LiteralConstant(new StringLiteral("This is a very long text that should be rendered on multiple lines")),
			)
		]);
	}

	public function testToString(): void {
		self::assertEquals(
str_replace("\r\n", "\n", '[
	boolean: true,
	real: 3.14,
	integer: 42,
	string: "Hello",
	null: null,
	type: Any,
	others: [Suit.Spade, ^Any => String @@ :: null],
	long: [
		"This is a very long text that should be rendered on multiple lines"
	]
]'),
			(string)$this->constant
		);
	}

	public function testToEncoding(): void {
		self::assertEquals(
			$this->constant,
			(new FromArraySourceBuilder)->build(
				json_decode(json_encode($this->constant), true))
		);
	}
}