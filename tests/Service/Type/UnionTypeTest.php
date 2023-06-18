<?php

namespace Cast\Test\Service\Type;

use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Service\Type\SubtypeRelationChecker;
use PHPUnit\Framework\TestCase;

final class UnionTypeTest extends TestCase {
	private readonly SubtypeRelationChecker $subtypeRelationChecker;

	protected function setUp(): void {
		parent::setUp();

		$this->subtypeRelationChecker = new SubtypeRelationChecker;
	}

	public function testTypeToUnionType(): void {
		$i = new IntegerType(new IntegerRange(0, 30));
		$r = new RealType(new RealRange(0, 30));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$i,
			new UnionType($i, $r)
		));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$r,
			new UnionType($i, $r)
		));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$i,
			new UnionType($i, new BooleanType)
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			$r,
			new UnionType($i, new BooleanType)
		));
	}

	public function testUnionTypeToType(): void {
		$i = new IntegerType(new IntegerRange(0, 30));
		$r = new RealType(new RealRange(0, 30));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			new UnionType($i, $r),
			$i
		));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			new UnionType($i, $r),
			$r
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			new UnionType($i, $r),
			new BooleanType
		));
	}

	public function testUnionTypeToUnionType(): void {
		$i = new IntegerType(new IntegerRange(0, 30));
		$r = new RealType(new RealRange(0, 30));
		$b = new BooleanType;
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			new UnionType($i, $r),
			new UnionType($i, $r, $b),
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			new UnionType($i, $r, $b),
			new UnionType($i, $r),
		));
	}
}