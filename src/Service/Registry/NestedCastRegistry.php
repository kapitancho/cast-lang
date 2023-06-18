<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Runtime\TypeCast;

final readonly class NestedCastRegistry implements CastRegistry {
	/** @var array<string, TypeCast> $casts */
	private array $casts;
	/** @var array<string, array<string, TypeCast>> $castsBySourceType */
	private array $castsBySourceType;
	/** @var array<string, array<string, TypeCast>> $castsByTargetType */
	private array $castsByTargetType;

	public function __construct(
		private CastRegistry $parentCastRegistry,
		TypeCast ... $casts
	) {
		$castStorage = [];
		$castsBySourceType = [];
		$castsByTargetType = [];
		foreach($casts as $cast) {
			$castStorage[sprintf("%s:%s",
				$cast->castFromType, $cast->castToType)
			] = $cast;
			$castsBySourceType[$cast->castFromType->identifier] ??= [];
			$castsBySourceType[$cast->castFromType->identifier][$cast->castToType->identifier] = $cast;
			$castsByTargetType[$cast->castToType->identifier] ??= [];
			$castsByTargetType[$cast->castToType->identifier][$cast->castFromType->identifier] = $cast;
		}
		$this->casts = $castStorage;
		$this->castsBySourceType = $castsBySourceType;
		$this->castsByTargetType = $castsByTargetType;
	}

	/** @return TypeCast[] */
	public function getAllCasts(): array {
		return [... $this->casts, ... $this->parentCastRegistry->getAllCasts()];
	}

	/** @return TypeCast[] */
	public function getAllCastsFrom(TypeNameIdentifier $fromType): array {
		$k = $fromType->identifier;
		return [... $this->castsBySourceType[$k] ?? [],
			...$this->parentCastRegistry->getAllCastsFrom($fromType)];
	}

	/** @return TypeCast[] */
	public function getAllCastsTo(TypeNameIdentifier $toType): array {
		$k = $toType->identifier;
		return [... $this->castsByTargetType[$k] ?? [],
			...$this->parentCastRegistry->getAllCastsTo($toType)];
	}

	public function getCast(
		TypeNameIdentifier $fromType,
		TypeNameIdentifier $toType
	): TypeCast|NoCastAvailable {
		$k = sprintf("%s:%s", $fromType, $toType);
		return $this->casts[$k] ?? $this->parentCastRegistry->getCast($fromType, $toType);
	}
}