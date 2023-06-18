<?php

namespace Cast\Service\Type;

use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\Type;

final readonly class IntersectionTypeNormalizer {

	public function __construct(
		private SubtypeRelationChecker $subtypeRelationChecker
	) {}

	public function __invoke(Type ... $types): Type {
		$parsedTypes = $this->parseTypes($types);
		if (count($parsedTypes) === 0) {
			return new AnyType();
		}
		if (count($parsedTypes) === 1) {
			return $parsedTypes[0];
		}
		return new IntersectionType(...$parsedTypes);
	}

	/**
	 * @param Type[] $types
	 * @return Type[]
	 */
	private function parseTypes(array $types): array {
		$queue = [];
		foreach($types as $type) {
			$pTypes = $type instanceof IntersectionType ?
				$this->parseTypes($type->types) : [$type];
			foreach($pTypes as $tx) {
				foreach($queue as $qt) {
					if ($this->subtypeRelationChecker->isSubtype($qt, $tx)) {
						continue 2;
					}
				}
				for($ql = count($queue) - 1; $ql >= 0; $ql--) {
					$q = $queue[$ql];
					if ($this->subtypeRelationChecker->isSubtype($tx, $q)) {
						array_splice($queue, $ql, 1);
					} else if ($q instanceof IntegerType && $tx instanceof IntegerType) {
						$newRange = $q->range->tryRangeIntersectionWith($tx->range);
						if ($newRange) {
							array_splice($queue, $ql, 1);
							$tx = new IntegerType($newRange);
						} else {
							return [new NothingType];
						}
					}
				}
				$queue[] = $tx;
			}
		}
		return $queue;
	}
}