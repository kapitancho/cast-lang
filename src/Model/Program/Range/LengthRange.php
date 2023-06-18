<?php

namespace Cast\Model\Program\Range;

final readonly class LengthRange implements Range {
	public function __construct(
		public int              $minLength,
		public int|PlusInfinity $maxLength
	) {
		if (
			$this->minLength < 0 || (
				is_int($maxLength) && $maxLength < $minLength
			)
		) {
			throw new InvalidLengthRange($minLength, $maxLength);
		}
	}

	public function isSubRangeOf(LengthRange $range): bool {
		return
			$this->compare($this->minLength, $range->minLength) > -1 &&
			$this->compare($this->maxLength, $range->maxLength) < 1;
	}

	/** @return -1|0|1 */
	private function compare(int|PlusInfinity $a, int|PlusInfinity $b): int {
		if ($a === $b) { return 0; }
		if ($a instanceof PlusInfinity) { return 1; }
		if ($b instanceof PlusInfinity) { return -1; }
		return $a <=> $b;
	}

	public function __toString(): string {
		return ($this->minLength === 0 ? '' : $this->minLength) . '..' .
			($this->maxLength === PlusInfinity::value ? '' : $this->maxLength);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'range',
			'type' => 'length_range',
			'min_length' => $this->minLength,
			'max_length' => $this->maxLength
		];
	}
}