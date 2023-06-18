<?php

namespace Cast\Model\Program;

final readonly class ModuleComment implements Node {

	public function __construct(
		public string $commentText
	) {}

	public function __toString(): string {
		return sprintf("//%s\n", $this->commentText);
	}

	public function jsonSerialize(): array {
		return ['node' => 'comment'];
	}
}