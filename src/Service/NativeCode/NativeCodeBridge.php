<?php

namespace Cast\Service\NativeCode;

use Cast\Service\Expression\AnalyserException;

final readonly class NativeCodeBridge {
	/**
	 * @param array<string, NativeCodeHandler> $handlers
	 */
	public function __construct(private array $handlers) {}

	public function getById(string $operationId): NativeCodeHandler {
		return $this->handlers[$operationId] ?? throw new AnalyserException(
			sprintf("Invalid operation %s", $operationId)
		);
	}
}