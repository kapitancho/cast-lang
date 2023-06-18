<?php

namespace Cast\Model\Program\Term;


use Cast\Model\Program\Type\TypeNameTerm;

final readonly class ConstructorCallTerm implements Term {
	public function __construct(
		public TypeNameTerm $type,
		public Term     $parameter,
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

	public function jsonSerialize(): array {
		return [
			'node' => 'term',
			'term' => 'constructor_call',
			'target_type' => $this->type,
			'parameter' => $this->parameter
		];
	}
}