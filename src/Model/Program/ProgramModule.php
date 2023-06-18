<?php

namespace Cast\Model\Program;

use Cast\Model\Program\Identifier\ModuleNameIdentifier;
use Generator;

final readonly class ProgramModule implements ProgramNode {
	public function __construct(
		public ModuleNameIdentifier $moduleName,
		ModuleExpression|ModuleComment ... $moduleElements
	) {
		$this->moduleElements = $moduleElements;
	}

	/** @var array<ModuleExpression|ModuleComment> */
	public array $moduleElements;

	/** @return Generator<ModuleExpression> */
	public function expressions(): Generator {
		foreach ($this->moduleElements as $moduleElement) {
			if ($moduleElement instanceof ModuleExpression) {
				yield $moduleElement;
			}
		}
	}

	public function __toString(): string {
		$result = [];
		foreach($this->moduleElements as $moduleElement) {
			$result[] = sprintf("%s", $moduleElement);
		}
		return sprintf("-> %s;\n\n%s",
			$this->moduleName,
			implode($result)
		);
	}

	public function jsonSerialize(): array {
		return [
			'node' => 'module',
			'module_name' => $this->moduleName,
			'elements' => $this->moduleElements
		];
	}
}