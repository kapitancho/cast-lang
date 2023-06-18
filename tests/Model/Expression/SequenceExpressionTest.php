<?php

namespace Cast\Test\Model\Expression;

use Cast\Model\Program\Term\RecordTerm;
use Cast\Model\Program\Term\SequenceTerm;
use PHPUnit\Framework\TestCase;

final class SequenceExpressionTest extends TestCase {

	public function testEmptyExpression(): void {
		self::assertCount(1, (new SequenceTerm)->terms);
	}

	public function testLast(): void {
		self::assertEquals(
			new RecordTerm,
			(new SequenceTerm(
				new SequenceTerm,
				new RecordTerm
			))->last()
		);
	}

	public function testCount(): void {
		self::assertCount(
			2,
			(new SequenceTerm(
				new SequenceTerm,
				new RecordTerm
			))->terms
		);
	}
}