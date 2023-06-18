<?php

namespace Cast\Service;

use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Program\Program;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Builder\ProgramRuntimeBuilder;
use Cast\Service\Compiler\ProgramContext;

final readonly class Runner {

	public function __construct(
		public Program $program,
		private ProgramContext $programContext,
		private ProgramRuntimeBuilder $programRuntimeBuilder,
	) {}

	public function run(string $mainMethodName, Value $args): Value {
		$this->programRuntimeBuilder->getProgramAnalyser()->analyseProgram($this->programContext);

		return $this->programRuntimeBuilder->getProgramExecutor()->executeProgram(
			$this->programContext,
			new VariableNameIdentifier($mainMethodName),
			$args
		);
	}
}