<?php

namespace Cast\Model\Program\Expression;

final readonly class MatchExpression implements Expression {
	/** @var array<MatchPairExpression> $parameters */
	public array $parameters;
	public function __construct(
		public Expression $target,
		public MatchExpressionOperation $operation,
		MatchPairExpression ... $parameters,
	) {
		$this->parameters = $parameters;
	}

	public function asString(bool $multiline): string {
		$result = [];
		foreach($this->parameters as $parameter) {
			$result[] = str_replace("\n", "\n" . "\t", $parameter);
		}
		return sprintf("(%s) %s? {" . ($multiline ? "\n\t| " : "") . "%s" . ($multiline ? "\n" : "") . "}",
			$this->target,
			$this->operation === MatchExpressionOperation::equals ? '=' : '<:',
			implode( ($multiline ? "\n" . "\t" : " ") . '| ', $result));
	}

	public function __toString(): string {
		$result = $this->asString(false);
		return mb_strlen($result) > 40 ? $this->asString(true) : $result;
	}
}