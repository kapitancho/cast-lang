<?php

namespace Cast\Model\Program\Range;

final readonly class IntegerRange implements Range {
	public function __construct(
		public int|MinusInfinity $minValue,
		public int|PlusInfinity  $maxValue
	) {
		if (is_int($maxValue) && $maxValue < $minValue) {
			throw new InvalidIntegerRange($minValue, $maxValue);
		}
	}

	public function isSubRangeOf(IntegerRange $range): bool {
		return
			$this->compare($this->minValue, $range->minValue) > -1 &&
			$this->compare($this->maxValue, $range->maxValue) < 1;
	}

	private function min(
		MinusInfinity|PlusInfinity|int $value1,
		MinusInfinity|PlusInfinity|int $value2
	): MinusInfinity|PlusInfinity|int {
		return match(true) {
			$value1 === MinusInfinity::value || $value2 === MinusInfinity::value => MinusInfinity::value,
			$value1 === PlusInfinity::value => $value2,
			$value2 === PlusInfinity::value => $value1,
			default => min($value1, $value2)
		};
	}

	private function max(
		MinusInfinity|PlusInfinity|int $value1,
		MinusInfinity|PlusInfinity|int $value2
	): MinusInfinity|PlusInfinity|int {
		return match(true) {
			$value1 === PlusInfinity::value || $value2 === PlusInfinity::value => PlusInfinity::value,
			$value1 === MinusInfinity::value => $value2,
			$value2 === MinusInfinity::value => $value1,
			default => max($value1, $value2)
		};
	}

	public function intersectsWith(IntegerRange $range): bool {
		if (is_int($this->maxValue) && is_int($range->minValue) && $this->maxValue < $range->minValue) {
			return false;
		}
		if (is_int($this->minValue) && is_int($range->maxValue) && $this->minValue > $range->maxValue) {
			return false;
		}
		return true;
	}

	public function tryRangeUnionWith(IntegerRange $range): IntegerRange|null {
		return $this->intersectsWith($range) ? new self (
			$this->min($this->minValue, $range->minValue),
			$this->max($this->maxValue, $range->maxValue)
		) : null;
	}

	public function tryRangeIntersectionWith(IntegerRange $range): IntegerRange|null {
		return $this->intersectsWith($range) ? new self (
			$this->max($this->minValue, $range->minValue),
			$this->min($this->maxValue, $range->maxValue)
		) : null;
	}

	/** @return -1|0|1 */
	private function compare(int|MinusInfinity|PlusInfinity $a, int|MinusInfinity|PlusInfinity $b): int {
		if ($a === $b) { return 0; }
		if ($a instanceof MinusInfinity || $b instanceof PlusInfinity) { return -1; }
		if ($a instanceof PlusInfinity || $b instanceof MinusInfinity) { return 1; }
		return $a <=> $b;
	}

	public function __toString(): string {
		return
			($this->minValue === MinusInfinity::value ? '' : $this->minValue) . '..' .
			($this->maxValue === PlusInfinity::value ? '' : $this->maxValue);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'range',
			'type' => 'integer_range',
			'min_value' => $this->minValue,
			'max_value' => $this->maxValue
		];
	}
}