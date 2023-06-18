<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FunctionType;
use Cast\Model\Runtime\Type\MapType;
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
 * @template-implements ByTypeMethodFinder<MapType>
 */
final readonly class MapMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	public function __construct(
		private TypeAssistant $typeAssistant,
		private UnionTypeNormalizer $unionTypeNormalizer,
		private SubtypeRelationChecker $subtypeRelationChecker,
	) {}

	private function mapItemNotFound(): Type {
		return $this->typeAssistant->typeByName(new TypeNameIdentifier("MapItemNotFound"));
	}

	/** return Type[] */
	private function getMapTypes(Type $type): array {
		return match(true) {
			$type instanceof RecordType => $type->types,
			$type instanceof MapType => [$type->itemType],
			$type instanceof SubtypeType => $this->getMapTypes($type->baseType()),
			$type instanceof AliasType => $this->getMapTypes($type->aliasedType()),
			default => []
		};
	}

	private function getMinLength(Type $type): int {
		return match(true) {
			$type instanceof RecordType => count($type->types),
			$type instanceof MapType => $type->range->minLength,
			$type instanceof SubtypeType => $this->getMinLength($type->baseType()),
			$type instanceof AliasType => $this->getMinLength($type->aliasedType()),
			$type instanceof UnionType => min(array_map($this->getMinLength(...), $type->types)),
			default => 0
		};
	}

	private function getMaxLength(Type $type): int|PlusInfinity {
		return match(true) {
			$type instanceof RecordType => count($type->types),
			$type instanceof MapType => $type->range->maxLength,
			$type instanceof SubtypeType => $this->getMaxLength($type->baseType()),
			$type instanceof AliasType => $this->getMaxLength($type->aliasedType()),
			$type instanceof UnionType => max(array_map($this->getMaxLength(...), $type->types)),
			default => PlusInfinity::value
		};
	}

	/**
	 * @param Type $originalTargetType
	 * @param MapType $targetType
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
		if ($originalTargetType instanceof RecordType && $methodName->identifier === 'with') {
			if ($parameterType instanceof RecordType) {
				$recTypes = [...$originalTargetType->types, ...$parameterType->types];
				$newType = new RecordType(... $recTypes);
				return $this->getFunction("Record::with", $newType);
			}
		}
		if ($methodName->identifier === 'values') {
			return $this->getFunction("Map::values", new ArrayType(
				$targetType->itemType, $targetType->range
			));
		}
		if ($methodName->identifier === 'keys') {
			return $this->getFunction("Map::keys", new ArrayType(
				StringType::base(), $targetType->range
			));
		}
		if ($methodName->identifier === 'flip' &&
			$this->subtypeRelationChecker->isSubtype(
				$targetItemType, StringType::base()
			)
		) {
			return $this->getFunction("Map::flip", $targetType);
		}
		if ($parameterType instanceof FunctionType) {
			if ($methodName->identifier === 'map' &&
				$this->subtypeRelationChecker->isSubtype(
					$targetType->itemType,
					$parameterType->parameterType
				)
			) {
				return $this->getFunction("Map::map", new MapType(
					$parameterType->returnType->returnType,
					$targetType->range
				), $parameterType->returnType->errorType);
			}
			if ($methodName->identifier === 'mapKeyValue' &&
				$parameterType->parameterType instanceof RecordType &&
				$parameterType->returnType->returnType instanceof RecordType &&
				array_key_exists('key', $parameterType->parameterType->types) &&
				array_key_exists('value', $parameterType->parameterType->types) &&
				$this->subtypeRelationChecker->isSubtype(
					$parameterType->parameterType->types['key'],
					StringType::base()
				) &&
				array_key_exists('key', $parameterType->returnType->returnType->types) &&
				array_key_exists('value', $parameterType->returnType->returnType->types) &&
				$this->subtypeRelationChecker->isSubtype(
					$parameterType->returnType->returnType->types['key'],
					StringType::base()
				) &&
				$this->subtypeRelationChecker->isSubtype(
					$targetType->itemType,
					$parameterType->parameterType->types['value']
				)
			) {
				return $this->getFunction("Map::mapKeyValue", new MapType(
					$parameterType->returnType->returnType->types['value'],
					$targetType->range
				), $parameterType->returnType->errorType);
			}
			if ($methodName->identifier === 'filter' &&
				$this->subtypeRelationChecker->isSubtype(
					$parameterType->returnType->returnType,
					new BooleanType
				) &&
				$this->subtypeRelationChecker->isSubtype(
					$targetType->itemType,
					$parameterType->parameterType
				)
			) {
				return $this->getFunction("Map::filter", new MapType(
					$targetType->itemType,
					new LengthRange(0, $targetType->range->maxLength),
				), $parameterType->returnType->errorType);
			}
			if ($methodName->identifier === 'findFirst') {
				return $this->getFunction("Map::findFirst",
					$targetType->itemType,
					new ErrorType(
						errorType: $this->mapItemNotFound()
					)
				);
			}
			if ($methodName->identifier === 'findFirstKeyValue') {
				return $this->getFunction("Map::findFirstKeyValue",
					new RecordType(... [
						'key' => StringType::base(),
						'value' => $targetType->itemType,
					]),
					new ErrorType(
						errorType: $this->mapItemNotFound()
					)
				);
			}
			if ($methodName->identifier === 'filterKeyValue' &&
				$parameterType->parameterType instanceof RecordType &&
				$this->subtypeRelationChecker->isSubtype(
					$parameterType->returnType->returnType,
					new BooleanType
				) &&
				array_key_exists('key', $parameterType->parameterType->types) &&
				array_key_exists('value', $parameterType->parameterType->types) &&
				$this->subtypeRelationChecker->isSubtype(
					$parameterType->parameterType->types['key'],
					StringType::base()
				) &&
				$this->subtypeRelationChecker->isSubtype(
					$targetType->itemType,
					$parameterType->parameterType->types['value']
				)
			) {
				return $this->getFunction("Map::filterKeyValue", new MapType(
					$targetType->itemType,
					new LengthRange(0, $targetType->range->maxLength),
				), $parameterType->returnType->errorType);
			}
		}
		if ($methodName->identifier === 'mergeWith' &&
			$this->subtypeRelationChecker->isSubtype($parameterType, MapType::base())
		) {
			$types = $this->getMapTypes($parameterType);
			$minLength = $this->getMinLength($parameterType);
			$maxLength = $this->getMaxLength($parameterType);
			return $this->getFunction("Map::mergeWith", new MapType(
				$this->unionTypeNormalizer->normalize(
					$targetType->itemType,
					... $types
				),
				new LengthRange(
					max(
						$targetType->range->minLength,
						$minLength
					),
					$maxLength === PlusInfinity::value ||
					$maxLength === PlusInfinity::value ? PlusInfinity::value :
						$targetType->range->maxLength + $maxLength,
				)
			));
		}
		if ($parameterType instanceof RecordType) { //TODO - with for Record
			if ($methodName->identifier === 'withKeyValue' &&
				array_key_exists('key', $parameterType->types) &&
				array_key_exists('value', $parameterType->types) &&
				$this->subtypeRelationChecker->isSubtype(
					$parameterType->types['key'], StringType::base()
				)
			) {
				return $this->getFunction("Map::withKeyValue", new MapType(
					$this->unionTypeNormalizer->normalize(
						$targetType->itemType,
						$parameterType->types['value']
					),
					new LengthRange(
						$targetType->range->minLength,
						$targetType->range->maxLength === PlusInfinity::value ?
							PlusInfinity::value : $targetType->range->maxLength + 1,
					)
				));
			}
		}
		if ($methodName->identifier === 'without') {
			return $this->getFunction("Map::without",
				new MapType(
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
					errorType: $this->mapItemNotFound()
				)
			);
		}
		if ($parameterType instanceof StringType) {
			if ($methodName->identifier === 'withoutByKey') {
				return $this->getFunction("Map::withoutByKey",
					new MapType(
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
						errorType: $this->mapItemNotFound()
					)
				);
			}
			if ($methodName->identifier === 'item') {
				return $this->getFunction("Map::item",
					$targetType->itemType,
					new ErrorType(
						errorType: $this->mapItemNotFound()
					)
				);
			}
			if ($methodName->identifier === 'keyExists') {
				return $this->getFunction("Map::keyExists", new BooleanType);
			}
		}
		if ($methodName->identifier === 'contains') {
			return $this->getFunction("Map::contains", new BooleanType);
		}
		if ($methodName->identifier === 'keyOf') {
			return $this->getFunction("Map::keyOf",
				StringType::base(),
				new ErrorType(
					errorType: $this->mapItemNotFound()
				)
			);
		}
		return NoMethodAvailable::value;
	}
}