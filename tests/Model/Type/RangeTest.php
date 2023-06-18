<?php

namespace Cast\Test\Model\Type;

use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\InvalidIntegerRange;
use Cast\Model\Program\Range\InvalidLengthRange;
use Cast\Model\Program\Range\InvalidRealRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;
use PHPUnit\Framework\TestCase;

final class RangeTest extends TestCase {

	public function testLengthRangeWithInfinity(): void {
		$mm = 0;
		$m = 2;
		$p = 5;
		$pp = PlusInfinity::value;
		$ranges = [
			new LengthRange($mm, $pp),
			new LengthRange($mm, $m),
			new LengthRange($mm, $p),
			new LengthRange($m, $pp),
			new LengthRange($p, $pp),
			new LengthRange($m, $p)
		];
		$this->checkMatrix($ranges);
	}

	public function testLengthRangeNegative(): void {
		$this->expectException(InvalidLengthRange::class);
		new LengthRange(-10, 10);
	}

	public function testLengthRangeInvalid(): void {
		$this->expectException(InvalidLengthRange::class);
		new LengthRange(10, 3);
	}

	public function testIntegerRangeWithInfinity(): void {
		$m = -5;
		$p = 5;
		$ranges = [
			new IntegerRange(MinusInfinity::value, PlusInfinity::value),
			new IntegerRange(MinusInfinity::value, $m),
			new IntegerRange(MinusInfinity::value, $p),
			new IntegerRange($m, PlusInfinity::value),
			new IntegerRange($p, PlusInfinity::value),
			new IntegerRange($m, $p)
		];
		$this->checkMatrix($ranges);
	}

	public function testIntegerRangeWithoutInfinity(): void {
		$mm = -1000;
		$m = -5;
		$p = 5;
		$pp = 1000;
		$ranges = [
			new IntegerRange($mm, $pp),
			new IntegerRange($mm, $m),
			new IntegerRange($mm, $p),
			new IntegerRange($m, $pp),
			new IntegerRange($p, $pp),
			new IntegerRange($m, $p)
		];
		$this->checkMatrix($ranges);
	}

	public function testIntegerRangeInvalid(): void {
		$this->expectException(InvalidIntegerRange::class);
		new IntegerRange(10, -10);
	}

	public function testRealRangeWithInfinity(): void {
		$m = -3.14;
		$p = 3.14;
		$ranges = [
			new RealRange(MinusInfinity::value, PlusInfinity::value),
			new RealRange(MinusInfinity::value, $m),
			new RealRange(MinusInfinity::value, $p),
			new RealRange($m, PlusInfinity::value),
			new RealRange($p, PlusInfinity::value),
			new RealRange($m, $p)
		];
		$this->checkMatrix($ranges);
	}

	public function testRealRangeWithoutInfinity(): void {
		$mm = -1000;
		$m = -3.14;
		$p = 3.14;
		$pp = 1000;
		$ranges = [
			new RealRange($mm, $pp),
			new RealRange($mm, $m),
			new RealRange($mm, $p),
			new RealRange($m, $pp),
			new RealRange($p, $pp),
			new RealRange($m, $p)
		];
		$this->checkMatrix($ranges);
	}

	public function testRealRangeInvalid(): void {
		$this->expectException(InvalidRealRange::class);
		new RealRange(10, -10);
	}

	/** @param list<IntegerRange>|list<RealRange> $ranges */
	private function checkMatrix(array $ranges): void {
		$matrix = [
			[1, 0, 0, 0, 0, 0],
			[1, 1, 1, 0, 0, 0],
			[1, 0, 1, 0, 0, 0],
			[1, 0, 0, 1, 0, 0],
			[1, 0, 0, 1, 1, 0],
			[1, 0, 1, 1, 0, 1],
		];
		foreach($ranges as $k0 => $range) {
			foreach($ranges as $k1 => $refRange) {
				self::assertEquals((bool)$matrix[$k0][$k1], $range->isSubRangeOf($refRange),
					sprintf("Range %s is a sub range of %s",
						$range, $refRange));
			}
		}
	}

}