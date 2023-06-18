<?php

namespace Cast\Test\Service\Type;

use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Service\Registry\TypeRegistry;
use Cast\Service\Type\IntersectionTypeNormalizer;
use Cast\Service\Type\SubtypeRelationChecker;
use PHPUnit\Framework\TestCase;

final class IntersectionTypeNormalizerTest extends TestCase {

	private readonly TypeRegistry $typeRegistry;
	private readonly IntersectionTypeNormalizer $normalizer;

	protected function setUp(): void {
		parent::setUp();

		$this->normalizer = new IntersectionTypeNormalizer(
			new SubtypeRelationChecker
		);
	}

	public function testEmptyIntersection(): void {
		self::assertEquals("Any", (string)($this->normalizer)());
	}

	public function testSingleType(): void {
		self::assertEquals("Boolean", (string)($this->normalizer)(
			new BooleanType
		));
	}

	public function testSimpleUnionType(): void {
		self::assertEquals("(Boolean&Integer)", (string)($this->normalizer)(
			new BooleanType,
			new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value))
		));
	}

	public function testWithAnyType(): void {
		self::assertEquals("(Boolean&Integer)", (string)($this->normalizer)(
			new BooleanType,
			new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value)),
			new AnyType
		));
	}

	public function testWithNothingType(): void {
		self::assertEquals("Nothing", (string)($this->normalizer)(
			new BooleanType,
			new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value)),
			new NothingType
		));
	}

	public function testWithNestedType(): void {
		self::assertEquals("(Boolean&Integer&String)", (string)($this->normalizer)(
			new BooleanType,
			new IntersectionType(
				new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value)),
				new StringType(new LengthRange(0, PlusInfinity::value)),
			)
		));
	}

	public function testSubtypes(): void {
		self::assertEquals("(Integer&False)", (string)($this->normalizer)(
			new BooleanType,
			new IntegerType(new IntegerRange(MinusInfinity::value, PlusInfinity::value)),
			new FalseType,
			new RealType(new RealRange(MinusInfinity::value, PlusInfinity::value))
		));
	}

	public function testDisjointRanges(): void {
		self::assertEquals("Nothing", (string)($this->normalizer)(
			new IntegerType(new IntegerRange(1, 10)),
			new IntegerType(new IntegerRange(15, 25)),
		));
	}

	public function testJointRanges(): void {
		self::assertEquals("Integer<10..15>", (string)($this->normalizer)(
			new IntegerType(new IntegerRange(1, 15)),
			new IntegerType(new IntegerRange(10, 25)),
		));
	}

	public function testJointRangesInfinity(): void {
		self::assertEquals("Integer<10..15>", (string)($this->normalizer)(
			new IntegerType(new IntegerRange(MinusInfinity::value, 15)),
			new IntegerType(new IntegerRange(10, PlusInfinity::value)),
		));
	}

}