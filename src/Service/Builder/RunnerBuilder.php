<?php

namespace Cast\Service\Builder;

use Cast\Service\Compiler\ProgramSourceCompiler;
use Cast\Service\Compiler\SourceToJsonConverter;
use Cast\Service\Program\Builder\FromArraySourceBuilder;
use Cast\Service\Program\Builder\ProgramBuilder;
use Cast\Service\Runner;

final readonly class RunnerBuilder {

	public function __construct(
		private string $parserPath,
		private string $parserClassName,
		private string $sourceRoot,
		private string $jsonCacheDirectory,
	) {}

	public function getRunner(string ... $castSources): Runner {
		$sourceToJsonConverter = new SourceToJsonConverter(
			$this->parserPath,
			$this->parserClassName
		);
		$programSourceCompiler = new ProgramSourceCompiler(
			$this->sourceRoot,
			$this->jsonCacheDirectory,
			$sourceToJsonConverter
		);
		$programBuilder = new ProgramBuilder(
			new FromArraySourceBuilder,
			$programSourceCompiler
		);
		$program = $programBuilder->buildProgramFromSource(... $castSources);
		$programContext = (new ProgramContextBuilder)->buildForProgram($program);
		$programRuntimeBuilder = new ProgramRuntimeBuilder(
			(new LanguageContextBuilder)->buildContext(),
			$programContext
		);

		return new Runner($program, $programContext, $programRuntimeBuilder);
	}
}