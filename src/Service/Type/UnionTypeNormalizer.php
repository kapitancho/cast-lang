<?php

namespace Cast\Service\Type;

use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\UnionType;

final readonly class UnionTypeNormalizer {

	public function __construct(
		private SubtypeRelationChecker $subtypeRelationChecker
	) {}

	/**
	 * @template T of Type
	 * @param array<int, T> $types
	 * @return T
	 */
	public function __invoke(Type ... $types): Type {
		return $this->normalize(... $types);
	}

	/**
	 * @template T of Type
	 * @param array<int, T> $types
	 * @return T
	 */
	public function normalize(Type ... $types): Type {
		$parsedTypes = $this->parseTypes($types);
		if (count($parsedTypes) === 0) {
			return new NothingType;
		}
		if (count($parsedTypes) === 1) {
			return $parsedTypes[0];
		}
		return new UnionType(...$parsedTypes);
	}

	public function normalizeErrorType(ErrorType ... $types): ErrorType {
		$anyError = array_filter($types, static fn(ErrorType $type): bool =>
			$type->anyError) !== [];
		$type = $this->normalize(... array_map(
			static fn(ErrorType $type): Type => $type->errorType,
			$types
		));
		return new ErrorType($anyError, $type);
	}

	/**
	 * @param Type[] $types
	 * @return Type[]
	 */
	private function parseTypes(array $types): array {
		$queue = [];
		foreach($types as $type) {
			$xType = $type;
			while($xType instanceof AliasType) {
				$xType = $xType->aliasedType();
			}
			$pTypes = $xType instanceof UnionType ?
				$this->parseTypes($xType->types) : [$type];
			foreach($pTypes as $tx) {
				foreach($queue as $qt) {
					if ($this->subtypeRelationChecker->isSubtype($tx, $qt)) {
						continue 2;
					}
				}
				for($ql = count($queue) - 1; $ql >= 0; $ql--) {
					$q = $queue[$ql];
					if ($this->subtypeRelationChecker->isSubtype($q, $tx)) {
						array_splice($queue, $ql, 1);
					} else if ($q instanceof IntegerType && $tx instanceof IntegerType) {
						$newRange = $q->range->tryRangeUnionWith($tx->range);
						if ($newRange) {
							array_splice($queue, $ql, 1);
							$tx = new IntegerType($newRange);
						}
					}
				}
				$queue[] = $tx;
			}
		}
		return $queue;
	}

	public function toBaseType(Type ... $types): Type {
		if ($types instanceof UnionType) {
			return $this->toBaseType();
		}
		foreach([
			new BooleanType,
	        IntegerType::base(),
	        RealType::base(),
	        StringType::base(),
			ArrayType::base(),
			MapType::base(),
        ] as $baseTypeCandidate) {
			foreach($types as $type) {
				if (!$this->subtypeRelationChecker->isSubtype($type, $baseTypeCandidate)) {
					continue 2;
				}
			}
			return $baseTypeCandidate;
		}
		return new AnyType();
	}
}