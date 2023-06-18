<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Runtime\TypeCast;

final readonly class EmptyCastRegistry implements CastRegistry {
	/** @return TypeCast[] */
	public function getAllCasts(): array {
		return [];
	}
	/** @return TypeCast[] */
	public function getAllCastsFrom(TypeNameIdentifier $fromType): array {
		return [];
	}
	/** @return TypeCast[] */
	public function getAllCastsTo(TypeNameIdentifier $toType): array {
		return [];
	}
	public function getCast(
		TypeNameIdentifier $fromType,
		TypeNameIdentifier $toType
	): TypeCast|NoCastAvailable {
		return NoCastAvailable::value;
	}
}