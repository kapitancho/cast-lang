<?php

namespace Cast\Test\Model\Expression;

use Cast\Model\Program\Term\NativeCodeTerm;
use PHPUnit\Framework\TestCase;

final class NativeCodeExpressionTest extends TestCase {

	public function testSerialization(): void {
		self::assertEquals(
			'{"node":"term","term":"native","id":"NativeId"}',
			json_encode(new NativeCodeTerm('NativeId'), JSON_THROW_ON_ERROR)
		);
	}

	public function testToString(): void {
		self::assertEquals(
			'[native: NativeId]',
			(string)new NativeCodeTerm('NativeId')
		);
	}
}