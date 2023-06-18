<?php

namespace Cast\Model\Program\Term;



final readonly class MatchValueTerm implements Term {
	/** @var array<MatchPairTerm|DefaultMatchTerm> $parameters */
	public array $parameters;
	public function __construct(
		public Term                    $target,
		MatchPairTerm|DefaultMatchTerm ... $parameters,
	) {
		$this->parameters = $parameters;
	}

	public function asString(bool $multiline): string {
		$result = [];
		foreach($this->parameters as $parameter) {
			$result[] = str_replace("\n", "\n" . "\t", $parameter);
		}
		return sprintf("(%s) ?= {" . ($multiline ? "\n\t| " : "") . "%s" . ($multiline ? "\n" : "") . "}",
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
			'term' => 'match_value',
			'target' => $this->target,
			'parameters' => $this->parameters
		];
	}
}