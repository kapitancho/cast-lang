<?php

namespace Cast\Test\Service\Type;

use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Service\Type\SubtypeRelationChecker;
use PHPUnit\Framework\TestCase;

final class TupleTypeTest extends TestCase {
	private readonly SubtypeRelationChecker $subtypeRelationChecker;

	protected function setUp(): void {
		parent::setUp();

		$this->subtypeRelationChecker = new SubtypeRelationChecker;
	}

	public function testTupleType(): void {
		$t1 = new TupleType(new BooleanType, new RealType(new RealRange(0, 10)));
		$t2 = new TupleType(new TrueType, new IntegerType(new IntegerRange(0, 10)));
		$t3 = new TupleType(new BooleanType, StringType::base());

		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$t1, $t1
		));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$t2, $t1
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			$t1, $t3
		));
	}

	public function testDifferentSizes(): void {
		$t1 = new TupleType(
			new BooleanType,
			new RealType(new RealRange(0, 10))
		);
		$t2 = new TupleType(
			new TrueType,
			new IntegerType(new IntegerRange(0, 10)),
			StringType::base()
		);
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$t2, $t1
		));
	}

}