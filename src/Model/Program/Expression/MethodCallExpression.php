<?php

namespace Cast\Model\Program\Expression;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;

final readonly class MethodCallExpression implements Expression {
	public function __construct(
		public Expression $target,
		public PropertyNameIdentifier $methodName,
		public Expression $parameter,
	) {}

	public function asString(bool $multiline): string {
		$param = (string)$this->parameter;
		if ($param === 'null') {
			return sprintf("%s->%s",
				$this->target,
				$this->methodName);
		}
		if (str_starts_with($param, '[') && str_ends_with($param, ']')) {
			return sprintf("%s->%s%s", $this->target, $this->methodName, $param);
		}
		return $multiline ?
			sprintf("%s->%s(\n\t%s\n)", $this->target, $this->methodName, $param) :
			sprintf("%s->%s(%s)", $this->target, $this->methodName, $param);
	}

	public function __toString(): string {
		$result = $this->asString(false);
		return mb_strlen($result) > 60 ? $this->asString(true) : $result;
	}
}