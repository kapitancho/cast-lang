<?php

namespace Cast\Model\Program\Type;

final readonly class RecordTypeTerm implements TypeTerm {
	/** @var array<string, TypeTerm>  */
	public array $types;

	public function __construct(
		TypeTerm ... $types,
	) {
		$this->types = $types;
	}

	public function propertyByKey(string $key): TypeTerm|null {
		return $this->types[$key] ?? null;
	}

	public function asString(bool $multiline): string {
		$result = [];
		foreach($this->types as $key => $value) {
			$type = ucfirst($key) === (string)$value ?
				sprintf("~%s", $value) :
				sprintf("%s: %s", $key, $value);
			$result[] = $multiline ?
				"\t" . str_replace("\n", "\n" . "\t", $type) : $type;
		}
		return $multiline ?
			sprintf("[\n%s\n]", implode("," . "\n", $result)) :
			sprintf("[%s]", implode(", ", $result) ?: ':');
	}

	public function __toString(): string {
		$result = $this->asString(false);
		return mb_strlen($result) > 40 ? $this->asString(true) : $result;
	}

		public function jsonSerialize(): array {
		return [
			'node' => 'type_term',
			'type' => 'record',
			'types' => $this->types
		];
	}
}