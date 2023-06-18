<?php

namespace Cast\Test\Service\Type;

use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Service\Type\SubtypeRelationChecker;
use PHPUnit\Framework\TestCase;

final class IntersectionTypeTest extends TestCase {
	private readonly SubtypeRelationChecker $subtypeRelationChecker;

	protected function setUp(): void {
		parent::setUp();

		$this->subtypeRelationChecker = new SubtypeRelationChecker;
	}

	public function testTypeToIntersectionType(): void {
		$i = new IntegerType(new IntegerRange(0, 30));
		$r = new RealType(new RealRange(0, 30));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$i,
			new IntersectionType($i, $r)
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			$r,
			new IntersectionType($i, $r)
		));
	}

	public function testIntersectionTypeToType(): void {
		$i = new IntegerType(new IntegerRange(0, 30));
		$r = new RealType(new RealRange(0, 30));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			new IntersectionType($i, $r),
			$i
		));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			new IntersectionType($i, $r),
			$r
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			new IntersectionType($i, $r),
			new BooleanType
		));
	}

	public function testIntersectionTypeToIntersectionType(): void {
		$i = new IntegerType(new IntegerRange(0, 30));
		$r = new RealType(new RealRange(0, 30));
		$b = new BooleanType;
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			new IntersectionType($i, $r, $b),
			new IntersectionType($i, $r),
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			new IntersectionType($i, $r),
			new IntersectionType($i, $r, $b),
		));
	}
}