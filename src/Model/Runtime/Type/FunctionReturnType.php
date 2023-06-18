<?php

namespace Cast\Model\Runtime\Type;

final readonly class FunctionReturnType {
	public function __construct(
		public Type      $returnType,
		public ErrorType $errorType = new ErrorType(false, new NothingType),
	) {}

	public function __toString(): string {
		$pieces = [(string)$this->returnType];
		$errorType = (string)$this->errorType;
		if ($errorType !== '') {
			$pieces[] = $errorType;
		}
		return implode(' ', $pieces);
	}

}