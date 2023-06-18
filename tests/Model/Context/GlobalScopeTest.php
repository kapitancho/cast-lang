<?php

namespace Cast\Test\Model\Context;

use Cast\Model\Context\TypedValue;
use Cast\Model\Context\VariableValueScope;
use Cast\Model\Context\VariableValuePair;
use Cast\Model\Context\UnknownContextVariable;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Value\LiteralValue;
use PHPUnit\Framework\TestCase;

final class GlobalScopeTest extends TestCase {
	private VariableValueScope $emptyScope;
	private VariableValueScope $nonEmptyScope;
	protected function setUp(): void {
		parent::setUp();

		$this->emptyScope = VariableValueScope::empty();
		$this->nonEmptyScope = VariableValueScope::fromValues(
			new VariableValuePair(
				new VariableNameIdentifier("x"),
				new TypedValue(
					new NullType,
					new LiteralValue(new NullLiteral)
				)
			)
		);
	}

	public function testVariables(): void {
		self::assertEquals([], $this->emptyScope->variables());
		self::assertEquals(['x'], $this->nonEmptyScope->variables());
	}

	public function testGetValue(): void {
		self::assertEquals(new LiteralValue(new NullLiteral),
			$this->nonEmptyScope->valueOf(
				new VariableNameIdentifier('x')
			)
		);
	}

	public function testGetType(): void {
		self::assertEquals(new NullType, $this->nonEmptyScope->typeOf(
			new VariableNameIdentifier('x')));
	}

	public function testGetVariable(): void {
		self::assertNotNull($this->nonEmptyScope->getVariable(
			new VariableNameIdentifier('x')));
		self::assertNotNull($this->nonEmptyScope->withAddedValues(
			new VariableValuePair(
				new VariableNameIdentifier("y"),
				new TypedValue(
					new NullType,
					new LiteralValue(new NullLiteral)
				)
			)
		)->getVariable(
			new VariableNameIdentifier('y')));
		$this->expectException(UnknownContextVariable::class);
		self::assertNotNull($this->emptyScope->getVariable(
			new VariableNameIdentifier('y')));
	}
}