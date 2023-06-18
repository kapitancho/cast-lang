<?php

namespace Cast\Model\Program\Expression;

final readonly class FunctionCallExpression implements Expression {
	public function __construct(
		public Expression $target,
		public Expression $parameter
	) {}

	public function asString(bool $multiline): string {
		$result = (string)$this->parameter;
		if ($result === 'null') {
			$result = '';
		}
		if (str_starts_with($result, '[') && str_ends_with($result, ']')) {
			return sprintf("%s%s", $this->target, $result);
		}
		return $multiline ?
			sprintf("%s(%s\n)", $this->target, "\n" . "\t" . $result) :
			sprintf("%s(%s)", $this->target, $result);
	}

	public function __toString(): string {
		$result = $this->asString(false);
		return mb_strlen($result) > 40 ? $this->asString(true) : $result;
	}
}