<?php

namespace Cast\Model\Program\Term;



final readonly class FunctionCallTerm implements Term {
	public function __construct(
		public Term $target,
		public Term $parameter
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

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'function_call',
			'target' => $this->target,
			'parameter' => $this->parameter
		];
	}
}