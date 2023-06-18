<?php

namespace Cast\Model\Program\Expression;

final readonly class SequenceExpression implements Expression {
	/** @var array<Expression> $expressions */
	public array $expressions;
	public function __construct(Expression ... $expressions) {
		$this->expressions = array_values($expressions) ?: [ConstantExpression::emptyExpression()];
	}

	public function last(): Expression {
		return $this->expressions[count($this->expressions) - 1];
	}

	public function __toString(): string {
		$result = [];
		foreach($this->expressions as $expression) {
			$result[] = str_replace("\n", "\n" . "\t", $expression);
		}
		return count($result) > 1 || strlen($result[0]) > 30 ?
			sprintf("{\n\t%s\n}", implode(";" . "\n" . "\t", $result)) :
			sprintf("{%s}", $result[0]);
	}
}