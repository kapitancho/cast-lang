<?php

namespace Cast\Test\Service\Type;

use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Service\Registry\TypeRegistry;
use Cast\Service\Type\SubtypeRelationChecker;
use PHPUnit\Framework\TestCase;

final class TypeTypeTest extends TestCase {
	private readonly SubtypeRelationChecker $subtypeRelationChecker;

	protected function setUp(): void {
		parent::setUp();

		$this->subtypeRelationChecker = new SubtypeRelationChecker;
	}

	public function testTypeType(): void {
		$t1 = new TypeType(new BooleanType);
		$t2 = new TypeType(new TrueType);

		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$t1, $t1
		));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$t2, $t1
		));
		self::assertTrue($this->subtypeRelationChecker->isSubtype(
			$t2, $t2
		));
		self::assertFalse($this->subtypeRelationChecker->isSubtype(
			$t1, $t2
		));
	}
}