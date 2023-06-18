<?php

namespace Cast\Test\Service\Scope;

use Cast\Model\Context\VariablePair;
use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\TrueType;
use PHPUnit\Framework\TestCase;

final class VariableScopeTest extends TestCase {
	public function testTest(): void {
		$scope = VariableScope::fromVariablePairs(
			new VariablePair(
				new VariableNameIdentifier('x'),
				new AnyType
			),
			new VariablePair(
				new VariableNameIdentifier('y'),
				new BooleanType
			)
		);
		self::assertEquals(new AnyType, $scope->scope['x']);
		self::assertEquals(new BooleanType, $scope->scope['y']);

		$newScope = $scope->withAddedVariablePairs(
			new VariablePair(
				new VariableNameIdentifier('y'),
				new TrueType
			),
			new VariablePair(
				new VariableNameIdentifier('z'),
				new FalseType
			)
		);
		self::assertEquals(new AnyType, $newScope->scope['x']);
		self::assertEquals(new TrueType, $newScope->scope['y']);
		self::assertEquals(new FalseType, $newScope->scope['z']);
	}
}
