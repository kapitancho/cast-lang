<?php

namespace Cast\Test\Model\Identifier;

use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\IdentifierException;
use Cast\Model\Program\Identifier\ModuleNameIdentifier;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use PHPUnit\Framework\TestCase;

final class IdentifierTest extends TestCase {

	public function testEnumValueIdentifier(): void {
		self::assertNotNull(new EnumValueIdentifier("ItShouldStartWithUppercaseAndContainAToZAnd0To9"));
		$this->expectException(IdentifierException::class);
		new EnumValueIdentifier("itShouldNotStartWithLowercase");
	}

	public function testModuleNameIdentifier(): void {
		self::assertNotNull(new ModuleNameIdentifier("ItShouldStartWithUppercaseAndContainAToZAnd0To9"));
		$this->expectException(IdentifierException::class);
		new ModuleNameIdentifier("itShouldNotStartWithLowercase");
	}

	public function testPropertyNameIdentifier(): void {
		self::assertNotNull(new PropertyNameIdentifier("222"));
		self::assertNotNull(new PropertyNameIdentifier("ItShouldContainsAToZ0To9And_Underscore"));
		$this->expectException(IdentifierException::class);
		new PropertyNameIdentifier("OtherCharactersLike+AreNotAllowed");
	}

	public function testTypeNameIdentifier(): void {
		self::assertNotNull(new TypeNameIdentifier("ItShouldStartWithUppercaseAndContainAToZAnd0To9"));
		$this->expectException(IdentifierException::class);
		new TypeNameIdentifier("itShouldNotStartWithLowercase");
	}

	public function testVariableNameIdentifier(): void {
		self::assertNotNull(new VariableNameIdentifier("$"));
		self::assertNotNull(new VariableNameIdentifier("#"));
		self::assertNotNull(new VariableNameIdentifier("#recordProperty"));
		self::assertNotNull(new VariableNameIdentifier("itShouldStartWithLowercaseAndContainAToZAnd0To9"));
		$this->expectException(IdentifierException::class);
		new VariableNameIdentifier("ItShouldNotStartWithUppercase");
	}
}