<?php

namespace Cast\Model\Program\Expression;

use Cast\Model\Program\Identifier\TypeNameIdentifier;

final readonly class ConstructorCallExpression implements Expression {
	public function __construct(
		public TypeNameIdentifier $type,
		public Expression $parameter,
	) {}

	public function asString(bool $multiline): string {
		$result = (string)$this->parameter;
		if ($result === 'null') {
			$result = '';
		}
		if (str_starts_with($result, '[') && str_ends_with($result, ']')) {
			return $multiline ?
				sprintf("%s%s\n", $this->type, "\n" . "\t" . $result) :
				sprintf("%s%s", $this->type, $result);
		}
		return $multiline ?
			sprintf("%s(%s\n)", $this->type, "\n" . "\t" . $result) :
			sprintf("%s(%s)", $this->type, $result);
	}

	public function __toString(): string {
		$result = $this->asString(false);
		return mb_strlen($result) > 40 ? $this->asString(true) : $result;
	}

}