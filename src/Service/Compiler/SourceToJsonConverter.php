<?php

namespace Cast\Service\Compiler;

use JsonException;

/** @codeCoverageIgnoreStart */
final readonly class SourceToJsonConverter {

	/**
	 * @param string $parserPath
	 * @param class-string $parserClassName
	 */
	public function __construct(
		private string $parserPath,
		private string $parserClassName,
	) {}

	/** @throws JsonException */
	public function convertSourceToJson(string $sourceCode): string {
		require_once $this->parserPath;

		ini_set('memory_limit', '30M');
		set_time_limit(40);

		$parser = new ($this->parserClassName)($sourceCode) ;
		/** @noinspection PhpArgumentWithoutNamedIdentifierInspection */
		return json_encode($parser->match_module(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
	}

}
/** @codeCoverageIgnoreEnd */