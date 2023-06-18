<?php

namespace Cast\Test\Service\Type;

use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\EnumerationValueType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Service\Type\IntersectionTypeNormalizer;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeChainGenerator;
use Cast\Service\Type\UnionTypeNormalizer;
use PHPUnit\Framework\TestCase;

final class TypeChainGeneratorTest extends TestCase {
	private TypeChainGenerator $typeChainGenerator;

	protected function setUp(): void {
		parent::setUp();

		$subtypeRelationChecker = new SubtypeRelationChecker;
		$unionTypeNormalizer = new UnionTypeNormalizer($subtypeRelationChecker);
		$intersectionTypeNormalizer = new IntersectionTypeNormalizer($subtypeRelationChecker);

		$this->typeChainGenerator = new TypeChainGenerator(
			$unionTypeNormalizer
		);
	}

	public function testBooleanType(): void {
		self::assertEquals([new BooleanType, new AnyType], [...
			$this->typeChainGenerator->generateTypeChain(new BooleanType, new AnyType)
		]);
	}

	public function testTrueType(): void {
		self::assertEquals([new TrueType, new BooleanType, new AnyType], [...
			$this->typeChainGenerator->generateTypeChain(new TrueType, new AnyType)
		]);
	}

	public function testRealType(): void {
		self::assertEquals([RealType::base(), new AnyType], [...
			$this->typeChainGenerator->generateTypeChain(RealType::base(), new AnyType)
		]);

		$t = new RealType(new RealRange(-3.14, 3.14));
		self::assertEquals([$t, RealType::base(), new AnyType], [...
			$this->typeChainGenerator->generateTypeChain($t)
		]);
	}

	public function testIntegerType(): void {
		self::assertEquals([IntegerType::base(), RealType::base(), new AnyType], [...
			$this->typeChainGenerator->generateTypeChain(IntegerType::base())
		]);

		$t = new IntegerType(new IntegerRange(-42, 42));
		self::assertEquals([
			$t,
			IntegerType::base(),
			new RealType(new RealRange(-42, 42)),
			RealType::base(),
			new AnyType
		], [... $this->typeChainGenerator->generateTypeChain($t)]);
	}

	public function testStringType(): void {
		self::assertEquals([StringType::base(), new AnyType], [...
			$this->typeChainGenerator->generateTypeChain(StringType::base())
		]);

		$t = new StringType(new LengthRange(0, 20));
		self::assertEquals([$t, StringType::base(), new AnyType], [...
			$this->typeChainGenerator->generateTypeChain($t)
		]);
	}

	public function testIntersectionType(): void {
		$t = new IntersectionType(
			new BooleanType,
			IntegerType::base()
		);
		self::assertEquals([
			$t, new BooleanType, IntegerType::base(), RealType::base(), new AnyType
		], [... $this->typeChainGenerator->generateTypeChain($t)]);
	}

	public function testEnumerationValueType(): void {
		$et = new EnumerationType(
			new TypeNameIdentifier("EnumTest"),
			new EnumValueIdentifier("EnumValue")
		);
		$t = new EnumerationValueType(
			$et,
			new EnumValueIdentifier("EnumValue")
		);
		self::assertEquals([
			$t,
			$et,
			StringType::base(),
			new AnyType
		], [... $this->typeChainGenerator->generateTypeChain($t)]);
	}
}