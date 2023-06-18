<?php

namespace Cast\Service\Program\Builder;

use Cast\Model\Program\Program;
use Cast\Service\Compiler\ProgramSourceCompiler;

final readonly class ProgramBuilder {

	public function __construct(
		private FromArraySourceBuilder $arrayBasedBuilder,
		private ProgramSourceCompiler $programSourceCompiler
	) {}

	public function buildProgramFromSource(string ... $sources): Program {
		$arr = [];
		foreach($this->programSourceCompiler->compile($sources, forceRecompile: false) as $source) {
			/** @noinspection JsonEncodingApiUsageInspection */
			$arr[] = json_decode($source, true);
		}
		$result = $this->arrayBasedBuilder->build([
			'node' => 'program',
			'modules' => $arr
		]);
		/** @codeCoverageIgnoreStart */
		if (!($result instanceof Program)) {
			throw new BuilderException("Cannot build the program. An invalid source has been provided");
		}
		/** @codeCoverageIgnoreEnd */
		return $result;
	}

}