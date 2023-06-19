<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FunctionType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @template-implements ByTypeMethodFinder<ArrayType>
 */
final readonly class ArrayMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	public function __construct(
		private TypeAssistant $typeAssistant,
		private UnionTypeNormalizer $unionTypeNormalizer,
		private SubtypeRelationChecker $subtypeRelationChecker,
	) {}

	private function arrayItemNotFound(): Type {
		return $this->typeAssistant->typeByName(new TypeNameIdentifier("ArrayItemNotFound"));
	}

	/** return Type[] */
	private function getArrayTypes(Type $type): array {
		return match(true) {
			$type instanceof TupleType => $type->types,
			$type instanceof ArrayType => [$type->itemType],
			$type instanceof SubtypeType => $this->getArrayTypes($type->baseType()),
			$type instanceof AliasType => $this->getArrayTypes($type->aliasedType()),
			default => []
		};
	}

	private function getMinLength(Type $type): int {
		return match(true) {
			$type instanceof TupleType => count($type->types),
			$type instanceof ArrayType => $type->range->minLength,
			$type instanceof SubtypeType => $this->getMinLength($type->baseType()),
			$type instanceof AliasType => $this->getMinLength($type->aliasedType()),
			$type instanceof UnionType => min(array_map($this->getMinLength(...), $type->types)),
			default => 0
		};
	}

	private function getMaxLength(Type $type): int|PlusInfinity {
		return match(true) {
			$type instanceof TupleType => count($type->types),
			$type instanceof ArrayType => $type->range->maxLength,
			$type instanceof SubtypeType => $this->getMaxLength($type->baseType()),
			$type instanceof AliasType => $this->getMaxLength($type->aliasedType()),
			$type instanceof UnionType => max(array_map($this->getMaxLength(...), $type->types)),
			default => PlusInfinity::value
		};
	}

	private function getStringMinLength(Type $type): int {
		return match(true) {
			$type instanceof StringType => $type->range->minLength,
			$type instanceof SubtypeType => $this->getStringMinLength($type->baseType()),
			$type instanceof AliasType => $this->getStringMinLength($type->aliasedType()),
			$type instanceof UnionType => min(array_map($this->getStringMinLength(...), $type->types)),
			default => 0
		};
	}

	private function getStringMaxLength(Type $type): int|PlusInfinity {
		return match(true) {
			$type instanceof StringType => $type->range->maxLength,
			$type instanceof SubtypeType => $this->getStringMinLength($type->baseType()),
			$type instanceof AliasType => $this->getStringMinLength($type->aliasedType()),
			$type instanceof UnionType => max(array_map($this->getStringMaxLength(...), $type->types)),
			default => PlusInfinity::value
		};
	}

	/**
	 * @param Type $originalTargetType
	 * @param ArrayType $targetType
	 * @param PropertyNameIdentifier $methodName
	 * @param Type $parameterType
	 * @return FunctionValue|NoMethodAvailable
	 */
	public function findMethodFor(
		Type                   $originalTargetType,
		Type                   $targetType,
		PropertyNameIdentifier $methodName,
		Type                   $parameterType
	): FunctionValue|NoMethodAvailable {
		$targetItemType = $targetType->itemType;
		while($targetItemType instanceof AliasType) {
			$targetItemType = $targetItemType->aliasedType();
		}
		if ($methodName->identifier === 'asMap') {
			if ($this->subtypeRelationChecker->isSubtype(
				$originalTargetType,
				new ArrayType(
					new TupleType(
						StringType::base(),
						new AnyType
					),
					new LengthRange(0, PlusInfinity::value)
				)
			)) {
				$returnType = new AnyType;
				$minLength = 0;
				$maxLength = PlusInfinity::value;
				if ($originalTargetType instanceof ArrayType &&
					$originalTargetType->itemType instanceof TupleType &&
					count($originalTargetType->itemType->types) > 1
				) {
					$returnType = $originalTargetType->itemType->types[1];
					$minLength = $originalTargetType->range->minLength;
					$maxLength = $originalTargetType->range->maxLength;
				} elseif ($originalTargetType instanceof TupleType) {
					$returnTypes = [];
					$minLength = $maxLength = count($originalTargetType->types);
					foreach($originalTargetType->types as $tupleType) {
						if ($tupleType instanceof TupleType && count($tupleType->types) > 1) {
							$returnTypes[] = $tupleType->types[1];
						} else {
							break;
						}
					}
					if (count($returnTypes) === count($originalTargetType->types)) {
						$returnType = $this->unionTypeNormalizer->normalize(... $returnTypes);
					}
				}
				return $this->getFunction("Array::asMap", new MapType(
					$returnType,
					new LengthRange($minLength, $maxLength)
				));
			}
		}
		if ($methodName->identifier === 'length') {
			return $this->getFunction("Array::length", new IntegerType(
				new IntegerRange(
					$targetType->range->minLength,
					$targetType->range->maxLength,
				),
			));
		}
		if ($methodName->identifier === 'reverse') {
			return $this->getFunction("Array::reverse", $targetType);
		}
		if ($methodName->identifier === 'item') {
			if ($parameterType instanceof IntegerType) {
				return $this->getFunction("Array::item",
					$targetType->itemType,
					new ErrorType(
						errorType:
							$parameterType->range->maxValue !== PlusInfinity::value &&
							$parameterType->range->maxValue <= $targetType->range->minLength - 1 &&
							$parameterType->range->minValue >= 0
								? new NothingType : $this->arrayItemNotFound()
					)
				);
			}
		}
		if ($methodName->identifier === 'contains') {
			return $this->getFunction("Array::contains", new BooleanType);
		}
		if ($methodName->identifier === 'indexOf') {
			return $this->getFunction("Array::indexOf",
				new IntegerType(
					new IntegerRange(0, $targetType->range->maxLength - 1)
				),
				new ErrorType(
					errorType: $this->arrayItemNotFound()
				)
			);
		}
		if ($methodName->identifier === 'without') {
			return $this->getFunction("Array::without",
				new ArrayType(
					$targetType->itemType,
					new LengthRange(
						$targetType->range->maxLength === PlusInfinity::value ?
							$targetType->range->minLength : min(
								$targetType->range->minLength,
								$targetType->range->maxLength - 1
							),
						$targetType->range->maxLength === PlusInfinity::value ?
							PlusInfinity::value : $targetType->range->maxLength - 1,
					)
				),
				new ErrorType(
					errorType: $this->arrayItemNotFound()
				)
			);
		}
		if ($this->subtypeRelationChecker->isSubtype($targetItemType, StringType::base())) {
			if ($methodName->identifier === 'combineAsText' &&
				$this->subtypeRelationChecker->isSubtype($parameterType, StringType::base())
			) {
				$minLength = $this->getStringMinLength($targetItemType);
				$maxLength = $this->getStringMaxLength($targetItemType);
				return $this->getFunction("Array::combineAsText",
					new StringType(
						new LengthRange(
							$targetType->range->minLength * $minLength,
							$targetType->range->maxLength === PlusInfinity::value ||
							$maxLength === PlusInfinity::value ? PlusInfinity::value :
								$targetType->range->maxLength * $maxLength,
						),
					)
				);
			}
			if ($methodName->identifier === 'countValues') {
				return $this->getFunction("Array::countValues",
					new MapType(
						new IntegerType(
							new IntegerRange(1, $targetType->range->maxLength)
						),
						$targetType->range
					)
				);
			}
		}
		if ($parameterType instanceof IntegerType) {
			if ($methodName->identifier === 'withoutByIndex') {
				return $this->getFunction("Array::withoutByIndex",
					new ArrayType(
						$targetItemType,
						new LengthRange(
							$targetType->range->maxLength === PlusInfinity::value ?
								$targetType->range->minLength : min(
									$targetType->range->minLength,
									$targetType->range->maxLength - 1
								),
							$targetType->range->maxLength === PlusInfinity::value ?
								PlusInfinity::value : $targetType->range->maxLength - 1,
						)
					),
					new ErrorType(
						errorType: $parameterType->range->maxValue <= $targetType->range->minLength &&
							$parameterType->range->minValue >= 0 ?
							new NothingType : $this->arrayItemNotFound()
					)
				);
			}
		}
		if ($methodName->identifier === 'withoutFirst') {
			return $this->getFunction("Array::withoutFirst",
				new RecordType(... [
					'array' => new ArrayType(
						$targetItemType,
						new LengthRange(
							$targetType->range->maxLength === PlusInfinity::value ?
								$targetType->range->minLength :
							min($targetType->range->minLength, $targetType->range->maxLength - 1),
							$targetType->range->maxLength === PlusInfinity::value ?
								PlusInfinity::value : $targetType->range->maxLength - 1,
						)
					),
					'element' => $targetItemType
				]),
				new ErrorType(
					errorType: $targetType->range->minLength > 0 ?
						new NothingType : $this->arrayItemNotFound()
				)
			);
		}
		if ($methodName->identifier === 'withoutLast') {
			return $this->getFunction("Array::withoutLast",
				new RecordType(... [
					'array' => new ArrayType(
						$targetItemType,
						new LengthRange(
							$targetType->range->maxLength === PlusInfinity::value ?
								$targetType->range->minLength :
							min($targetType->range->minLength, $targetType->range->maxLength - 1),
							$targetType->range->maxLength === PlusInfinity::value ?
								PlusInfinity::value : $targetType->range->maxLength - 1,
						)
					),
					'element' => $targetItemType
				]),
				new ErrorType(
					errorType: $targetType->range->minLength > 0 ?
						new NothingType : $this->arrayItemNotFound()
				)
			);
		}
		if ($methodName->identifier === 'pad') {
			if ($parameterType instanceof RecordType &&
				array_key_exists('length', $parameterType->types) &&
				array_key_exists('value', $parameterType->types) &&
				$parameterType->types['length'] instanceof IntegerType &&
				$parameterType->types['length']->range->minValue > 0
			) {
				return $this->getFunction("Array::pad", new ArrayType(
					$this->unionTypeNormalizer->normalize(
						$targetType->itemType,
						$parameterType->types['value']
					),
					new LengthRange(
						max(
							$targetType->range->minLength,
							$parameterType->types['length']->range->minValue,
						),
						$targetType->range->maxLength === PlusInfinity::value ||
						$parameterType->types['length']->range->maxValue === PlusInfinity::value ?
							PlusInfinity::value : max(
								$targetType->range->maxLength,
								$parameterType->types['length']->range->maxValue,
							)
					)
				));
			}
		}
		if ($methodName->identifier === 'insertLast') {
			return $this->getFunction("Array::insertLast", new ArrayType(
				$this->unionTypeNormalizer->normalize(
					$targetType->itemType,
					$parameterType
				),
				new LengthRange(
					$targetType->range->minLength + 1,
					$targetType->range->maxLength === PlusInfinity::value ? PlusInfinity::value :
						$targetType->range->maxLength + 1,
				)
			));
		}
		if ($methodName->identifier === 'insertFirst') {
			return $this->getFunction("Array::insertFirst", new ArrayType(
				$this->unionTypeNormalizer->normalize(
					$targetType->itemType,
					$parameterType
				),
				new LengthRange(
					$targetType->range->minLength + 1,
					$targetType->range->maxLength === PlusInfinity::value ? PlusInfinity::value :
						$targetType->range->maxLength + 1,
				)
			));
		}
		if ($methodName->identifier === 'appendWith' &&
			$this->subtypeRelationChecker->isSubtype(
				$parameterType, ArrayType::base()
			)
		) {
			$types = $this->getArrayTypes($parameterType);
			$minLength = $this->getMinLength($parameterType);
			$maxLength = $this->getMaxLength($parameterType);
			return $this->getFunction("Array::appendWith", new ArrayType(
				$this->unionTypeNormalizer->normalize(
					$targetType->itemType,
					... $types
				),
				new LengthRange(
					$targetType->range->minLength + $minLength,
					$targetType->range->maxLength === PlusInfinity::value ||
						$maxLength === PlusInfinity::value ? PlusInfinity::value :
						$targetType->range->maxLength + $maxLength,
				)
			));
		}
		if ($methodName->identifier === 'sort') {
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, RealType::base()
			)) {
				return $this->getFunction("Array::sortAsNumber", $targetType);
			}
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, StringType::base()
			)) {
				return $this->getFunction("Array::sortAsString", $targetType);
			}
		}
		if ($methodName->identifier === 'unique') {
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, RealType::base()
			)) {
				return $this->getFunction("Array::uniqueAsNumber",
					new ArrayType(
						$targetType->itemType,
						new LengthRange(
							$targetType->range->maxLength === PlusInfinity::value ? 1 :
								min(1, $targetType->range->maxLength),
							$targetType->range->maxLength
						)
					)
				);
			}
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, StringType::base()
			)) {
				return $this->getFunction("Array::uniqueAsString",
					new ArrayType(
						$targetType->itemType,
						new LengthRange(
							$targetType->range->maxLength === PlusInfinity::value ? 1 :
								min(1, $targetType->range->maxLength),
							$targetType->range->maxLength
						)
					)
				);
			}
		}
		if ($methodName->identifier === 'sum') {
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, IntegerType::base()
			)) {
				return $this->getFunction("Array::sum", IntegerType::base());
			}
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, RealType::base()
			)) {
				return $this->getFunction("Array::sum", RealType::base());
			}
		}
		if ($methodName->identifier === 'min' && $targetType->range->minLength > 0) {
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, IntegerType::base()
			)) {
				return $this->getFunction("Array::min", IntegerType::base());
			}
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, RealType::base()
			)) {
				return $this->getFunction("Array::min", RealType::base());
			}
		}
		if ($methodName->identifier === 'max' && $targetType->range->minLength > 0) {
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, IntegerType::base()
			)) {
				return $this->getFunction("Array::max", IntegerType::base());
			}
			if ($this->subtypeRelationChecker->isSubtype(
				$targetItemType, RealType::base()
			)) {
				return $this->getFunction("Array::max", RealType::base());
			}
		}
		if ($methodName->identifier === 'flip' &&
			$this->subtypeRelationChecker->isSubtype(
				$targetItemType, StringType::base()
			)
		) {
			return $this->getFunction("Array::flip", new MapType(
				new IntegerType(
					new IntegerRange(0,
						$targetType->range->maxLength === PlusInfinity::value ?
							PlusInfinity::value : $targetType->range->maxLength - 1
					)
				),
				$targetType->range
			));
		}
		if ($parameterType instanceof FunctionType) {
			if ($methodName->identifier === 'map' &&
				$this->subtypeRelationChecker->isSubtype(
					$targetItemType,
					$parameterType->parameterType
				)
			) {
				return $this->getFunction("Array::map", new ArrayType(
					$parameterType->returnType->returnType,
					$targetType->range
				), $parameterType->returnType->errorType);
			}
			if ($methodName->identifier === 'mapIndexValue' &&
				$parameterType->parameterType instanceof RecordType &&
				array_key_exists('index', $parameterType->parameterType->types) &&
				array_key_exists('value', $parameterType->parameterType->types) &&
				$this->subtypeRelationChecker->isSubtype(
					new IntegerType(new IntegerRange(0, PlusInfinity::value)),
					$parameterType->parameterType->types['index'],
				) &&
				$this->subtypeRelationChecker->isSubtype(
					$parameterType->parameterType->types['value'],
					$targetItemType,
				)
			) {
				return $this->getFunction("Array::mapIndexValue", new ArrayType(
					$parameterType->returnType->returnType,
					$targetType->range
				), $parameterType->returnType->errorType);
			}
			if ($methodName->identifier === 'filter' &&
				$this->subtypeRelationChecker->isSubtype(
					$parameterType->returnType->returnType,
					new BooleanType
				) &&
				$this->subtypeRelationChecker->isSubtype(
					$targetItemType,
					$parameterType->parameterType
				)
			) {
				return $this->getFunction("Array::filter", new ArrayType(
					$targetType->itemType,
					new LengthRange(0, $targetType->range->maxLength),
				), $parameterType->returnType->errorType);
			}
			if ($methodName->identifier === 'findFirst') {
				return $this->getFunction("Array::findFirst",
					$targetType->itemType,
					new ErrorType(
						errorType: $this->arrayItemNotFound()
					)
				);
			}
		}

		return NoMethodAvailable::value;
	}
}