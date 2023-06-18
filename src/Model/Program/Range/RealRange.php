<?php

namespace Cast\Model\Program\Range;

final readonly class RealRange implements Range {
	public function __construct(
		public float|MinusInfinity $minValue,
		public float|PlusInfinity  $maxValue
	) {
		if (is_float($maxValue) && $maxValue < $minValue) {
			throw new InvalidRealRange($minValue, $maxValue);
		}
	}

	public function isSubRangeOf(RealRange $range): bool {
		return
			$this->compare($this->minValue, $range->minValue) > -1 &&
			$this->compare($this->maxValue, $range->maxValue) < 1;
	}

	/** @return -1|0|1 */
	private function compare(float|MinusInfinity|PlusInfinity $a, float|MinusInfinity|PlusInfinity $b): int {
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
			'type' => 'real_range',
			'min_value' => $this->minValue,
			'max_value' => $this->maxValue
		];
	}

}