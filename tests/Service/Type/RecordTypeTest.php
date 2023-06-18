<?php

namespace Cast\Test\Service\Type;

use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Service\Type\SubtypeRelationChecker;
use PHPUnit\Framework\TestCase;

final class RecordTypeTest extends TestCase {
	private readonly SubtypeRelationChecker $subtypeRelationChecker;

	protected function setUp(): void {
		parent::setUp();

		$this->subtypeRelationChecker = new SubtypeRelationChecker;
	}

	public function testRecordType(): void {
		$t1 = new RecordType(first: new BooleanType, second: new RealType(new RealRange(0, 10)));
		$t2 = new RecordType(first: new TrueType, second: new IntegerType(new IntegerRange(0, 10)));
		$t3 = new RecordType(new BooleanType);
		$t4 = new RecordType(first: new BooleanType, third: new RealType(new RealRange(0, 10)));

		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$t1, $t1
		));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$t2, $t1
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			$t1, $t3
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			$t1, $t4
		));
	}

	public function testDifferentSizes(): void {
		$t1 = new RecordType(
			first: new BooleanType,
			second: new RealType(new RealRange(0, 10))
		);
		$t2 = new RecordType(
			first: new TrueType,
			second: new IntegerType(new IntegerRange(0, 10)),
			third: StringType::base()
		);
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$t2, $t1
		));
	}

	public function testIntersectionOfRecords(): void {
		$t1 = new RecordType(
			first: new BooleanType,
			second: new RealType(new RealRange(0, 10.5))
		);
		$t2 = new RecordType(
			second: new IntegerType(new IntegerRange(8, 15)),
			third: StringType::base()
		);
		$t = new IntersectionType($t1, $t2);
		$t3 = new RecordType(
			first: new FalseType,
			second: new IntegerType(new IntegerRange(8, 10)),
			third: new StringType(new LengthRange(5, PlusInfinity::value))
		);
		$t4 = new RecordType(
			first: new FalseType,
			second: new IntegerType(new IntegerRange(8, 11)),
			third: new StringType(new LengthRange(5, PlusInfinity::value))
		);
		self::assertTrue($this->subtypeRelationChecker->isSubtype($t3, $t));
		self::assertFalse($this->subtypeRelationChecker->isSubtype($t4, $t));
	}
}