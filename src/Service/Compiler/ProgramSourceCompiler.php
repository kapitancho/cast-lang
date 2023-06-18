<?php

namespace Cast\Service\Compiler;

use JsonException;

final readonly class ProgramSourceCompiler {
	public function __construct(
		private string $sourceRoot,
		private string $jsonCacheDirectory,
		private SourceToJsonConverter $sourceToJsonConverter
	) {}

	/**
	 * @param string[] $sourceFiles
	 * @param bool $forceRecompile
	 * @return string[]
	 * @throws JsonException|CompilationException
	 */
	public function compile(array $sourceFiles, bool $forceRecompile = false): array {
		$jsonData = [];
		foreach($sourceFiles as $sourceFile) {
			$sourceFullName = $this->sourceRoot . '/' . $sourceFile;
			if (!file_exists($sourceFullName)) {
				throw new CompilationException(
					sprintf("Source file %s is not found", $sourceFile)
				);
			}
			$jsonCacheFile = $this->jsonCacheDirectory . '/' . basename($sourceFile) . '.json';
			if ($forceRecompile || !file_exists($jsonCacheFile) ||
				filemtime($jsonCacheFile) < filemtime($sourceFullName)
			) {
				$json = $this->sourceToJsonConverter->convertSourceToJson(
					file_get_contents($sourceFullName)
				);
				file_put_contents($jsonCacheFile, $json);
				$jsonData[] = $json;
			} else {
				$jsonData[] = file_get_contents($jsonCacheFile);
			}
		}
		return $jsonData;
	}
}