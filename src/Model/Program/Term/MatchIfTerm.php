<?php

namespace Cast\Model\Program\Term;



final readonly class MatchIfTerm implements Term {
	public function __construct(
		public Term $target,
		public Term $trueExpression,
		public Term $falseExpression,
	) {}

	public function asString(bool $multiline): string {
		$result = [];
		foreach([$this->trueExpression, $this->falseExpression] as $parameter) {
			$result[] = str_replace("\n", "\n" . "\t", $parameter);
		}
		if ($result[1] === 'null' && (
			$this->trueExpression instanceof ThrowTerm ||
			$this->trueExpression instanceof ReturnTerm
		)) {
			return sprintf("(%s) ? %s", $this->target, $result[0]);
		}
		return sprintf("(%s) ?== {" . ($multiline ? "\n\t| " : "") . "%s" . ($multiline ? "\n" : "") . "}",
			$this->target,
			implode( ($multiline ? "\n" . "\t" : " ") . '| ', $result));
	}

	public function __toString(): string {
		$result = $this->asString(false);
		return mb_strlen($result) > 40 ? $this->asString(true) : $result;
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'match_if',
			'target' => $this->target,
			'parameters' => [$this->trueExpression, $this->falseExpression]
		];
	}
}