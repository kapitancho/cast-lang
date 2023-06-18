<?php

namespace Cast\Test\Service\Type;

use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Service\Registry\TypeRegistry;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\UnionTypeNormalizer;
use PHPUnit\Framework\TestCase;

final class UnionTypeNormalizerTest extends TestCase {

	private readonly TypeRegistry $typeRegistry;
	private readonly UnionTypeNormalizer $normalizer;

	protected function setUp(): void {
		parent::setUp();

		$this->normalizer = new UnionTypeNormalizer(
			new SubtypeRelationChecker
		);
	}

	public function testEmptyUnion(): void {
		self::assertEquals("Nothing", (string)($this->normalizer)());
	}

	public function testSingleType(): void {
		self::assertEquals("Boolean", (string)($this->normalizer)(
			new BooleanType
		));
	}

	public function testSimpleUnionType(): void {
		self::assertEquals("(Boolean|Integer)", (string)($this->normalizer)(
			new BooleanType,
			new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value))
		));
	}

	public function testWithNothingType(): void {
		self::assertEquals("(Boolean|Integer)", (string)($this->normalizer)(
			new BooleanType,
			new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value)),
			new NothingType
		));
	}

	public function testWithAnyType(): void {
		self::assertEquals("Any", (string)($this->normalizer)(
			new BooleanType,
			new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value)),
			new AnyType
		));
	}

	public function testWithNestedType(): void {
		self::assertEquals("(Boolean|Integer|String)", (string)($this->normalizer)(
			new BooleanType,
			new UnionType(
				new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value)),
				new StringType(new LengthRange(0, PlusInfinity::value)),
			)
		));
	}

	public function testSubtypes(): void {
		self::assertEquals("(Boolean|Real)", (string)($this->normalizer)(
			new BooleanType,
			new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value)),
			new FalseType,
			new RealType(new RealRange(MinusInfinity::value, PlusInfinity::value))
		));
	}

	public function testDisjointRanges(): void {
		self::assertEquals("(Integer<1..10>|Integer<15..25>)", (string)($this->normalizer)(
			new IntegerType(new IntegerRange(1, 10)),
			new IntegerType(new IntegerRange(15, 25)),
		));
	}

	public function testJointRanges(): void {
		self::assertEquals("Integer<1..25>", (string)($this->normalizer)(
			new IntegerType(new IntegerRange(1, 15)),
			new IntegerType(new IntegerRange(10, 25)),
		));
	}

	public function testJointRangesInfinity(): void {
		self::assertEquals("Integer", (string)($this->normalizer)(
			new IntegerType(new IntegerRange(MinusInfinity::value, 15)),
			new IntegerType(new IntegerRange(10, PlusInfinity::value)),
		));
	}

	public function testBaseIntegerType(): void {
		self::assertEquals("Integer", (string)$this->normalizer->toBaseType(
			new IntegerType(new IntegerRange(-3, 4)),
			new IntegerType(new IntegerRange(6, 18)),
			new IntegerType(new IntegerRange(10, 25)),
			new IntegerType(new IntegerRange(38, 55)),
		));
	}

	public function testBaseRealType(): void {
		self::assertEquals("Real", (string)$this->normalizer->toBaseType(
			new IntegerType(new IntegerRange(-3, 4)),
			new IntegerType(new IntegerRange(6, 18)),
			new RealType(new RealRange(10, 25)),
			new IntegerType(new IntegerRange(38, 55)),
		));
	}

	public function testBaseBooleanType(): void {
		self::assertEquals("Boolean", (string)$this->normalizer->toBaseType(
			new TrueType,
			new FalseType
		));
	}

	public function testBaseStringType(): void {
		self::assertEquals("String", (string)$this->normalizer->toBaseType(
			new StringType(new LengthRange(3, 12)),
			new StringType(new LengthRange(16, 28)),
		));
	}

	public function testBaseArrayType(): void {
		self::assertEquals("Array", (string)$this->normalizer->toBaseType(
			new ArrayType(IntegerType::base(), new LengthRange(3, 12)),
			new ArrayType(RealType::base(), new LengthRange(16, 28)),
			new TupleType(IntegerType::base(), RealType::base())
		));
	}

}