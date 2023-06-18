<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Runtime\TypeCast;

interface CastRegistry {
	/** @return TypeCast[] */
	public function getAllCasts(): array;

	/** @return TypeCast[] */
	public function getAllCastsFrom(TypeNameIdentifier $fromType): array;

	/** @return TypeCast[] */
	public function getAllCastsTo(TypeNameIdentifier $toType): array;

	public function getCast(
		TypeNameIdentifier $fromType,
		TypeNameIdentifier $toType
	): TypeCast|NoCastAvailable;
}