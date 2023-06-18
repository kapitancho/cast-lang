<?php

namespace Cast\Model\Program;

final readonly class Program implements ProgramNode {
	/** @var ProgramModule[] */
	public array $modules;

	public function __construct(ProgramModule ... $modules) {
		$this->modules = $modules;
	}

	public function __toString(): string {
		return implode("\n\n\n", $this->modules);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'program',
			'modules' => $this->modules
		];
	}

}