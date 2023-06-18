<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Literal\RealLiteral;
use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\MutableType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\MutableValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Execution\Flow\ThrowResult;
use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Registry\NoCastAvailable;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\UnknownType;

final readonly class NestedHydrator implements Hydrator {

	public function __construct(
		private TypeAssistant $typeAssistant,
		private SubtypeRelationChecker $subtypeRelationChecker,
		private CastRegistry $castRegistry,
		private FunctionExecutor $functionExecutor,
		private SubExpressionExecutor $subExpressionExecutor
	) {}

	/** @throws HydrationException */
	public function hydrate(Value $value, Type $targetType, string $hydrationPath): Value {
		/** @var callable-string $fn */
		$fn = match($targetType::class) {
			AnyType::class => $this->hydrateAny(...),
			ArrayType::class => $this->hydrateArray(...),
			BooleanType::class => $this->hydrateBoolean(...),
			EnumerationType::class => $this->hydrateEnumeration(...),
			FalseType::class => $this->hydrateFalse(...),
			IntegerType::class => $this->hydrateInteger(...),
			IntersectionType::class => $this->hydrateIntersection(...),
			MapType::class => $this->hydrateMap(...),
			MutableType::class => $this->hydrateMutable(...),
			NullType::class => $this->hydrateNull(...),
			RealType::class => $this->hydrateReal(...),
			RecordType::class => $this->hydrateRecord(...),
			StringType::class => $this->hydrateString(...),
			TrueType::class => $this->hydrateTrue(...),
			TupleType::class => $this->hydrateTuple(...),
			TypeType::class => $this->hydrateType(...),
			UnionType::class => $this->hydrateUnion(...),
			default => match(true) {
				$targetType instanceof AliasType => $this->hydrateAlias(...),
				$targetType instanceof SubtypeType => $this->hydrateSubtype(...),
				default => $value
			}
		};
		return $fn($value, $targetType, $hydrationPath);
	}

	private function hydrateAny(Value $value, AnyType $targetType, string $hydrationPath): Value {
		return $value;
	}

	private function hydrateType(Value $value, TypeType $targetType, string $hydrationPath): TypeValue {
		if ($value instanceof LiteralValue && $value->literal instanceof StringLiteral) {
			try {
				$type = $this->typeAssistant->typeByName(
					new TypeNameIdentifier($value->literal->value)
				);
				if ($this->subtypeRelationChecker->isSubtype(
					$type,
					$targetType->refType
				)) {
					return new TypeValue($type);
				}
				throw new HydrationException(
					$value,
					$hydrationPath,
					sprintf("The type should be a subtype of %s", $targetType->refType)
				);
			} catch (UnknownType $ex) {
				throw new HydrationException(
					$value,
					$hydrationPath,
					"The string value should be a name of a valid type"
				);
			}
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			"The value should be a string, containing a name of a valid type"
		);
	}

	private function hydrateEnumeration(Value $value, EnumerationType $targetType, string $hydrationPath): EnumerationValue {
		$cast = $this->castRegistry->getCast(
			new TypeNameIdentifier('JsonValue'),
			$targetType->typeName()
		);
		if ($cast !== NoCastAvailable::value) {
			try {
				/** @var EnumerationValue */
				return $this->functionExecutor->executeCastFunction(
					$this->subExpressionExecutor,
					$cast,
					$value
				);
			} catch (ThrowResult $throwResult) {
				throw new HydrationException(
					$value,
					$hydrationPath,
					sprintf("Enumeration hydration failed: %s",
						$throwResult->v
					)
				);
			}
		}
		if ($value instanceof LiteralValue) {
			foreach([
				'String' => StringLiteral::class,
				'Integer' => IntegerLiteral::class,
				'Real' => RealLiteral::class,
				'Boolean' => BooleanLiteral::class,
				'Null' => NullLiteral::class,
			] as $typeName => $typeClass) {
				if ($value->literal instanceof $typeClass) {
					$cast = $this->castRegistry->getCast(
						new TypeNameIdentifier($typeName),
						$targetType->typeName()
					);
					if ($cast !== NoCastAvailable::value) {
						try {
							/** @var EnumerationValue */
							return $this->functionExecutor->executeCastFunction(
								$this->subExpressionExecutor,
								$cast,
								$value
							);
						} catch (ThrowResult $throwResult) {
							throw new HydrationException(
								$value,
								$hydrationPath,
								sprintf("Enumeration hydration failed: %s",
									$throwResult->v
								)
							);
						}
					}
				}
			}
			if ($value->literal instanceof StringLiteral) {
				$cast = $this->castRegistry->getCast(
					new TypeNameIdentifier('String'),
					$targetType->typeName()
				);
				if ($cast === NoCastAvailable::value) {
					foreach($targetType->values as $enumValue) {
						if ($enumValue->identifier === $value->literal->value) {
							return new EnumerationValue(
								$targetType->typeName(),
								$enumValue
							);
						}
					}
				}
			}
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			sprintf("The value should be a string with a value among %s",
				implode(', ', array_map(
					static fn(EnumValueIdentifier $identifier): string
						=> $identifier->identifier, $targetType->values))
			)
		);
	}

	private function hydrateIntersection(Value $value, IntersectionType $targetType, string $hydrationPath): Value {
		$values = [];
		foreach($targetType->types as $type) {
			$result = $this->hydrate($value, $type, $hydrationPath);
			if (!($result instanceof DictValue)) {
				throw new HydrationException(
					$value,
					$hydrationPath,
					"A record value is expected"
				);
			}
			/** @noinspection SlowArrayOperationsInLoopInspection */
			$values = array_merge($values, $result->items);
		}
		return new DictValue($values);
	}

	private function hydrateUnion(Value $value, UnionType $targetType, string $hydrationPath): Value {
		$exceptions = [];
		foreach($targetType->types as $type) {
			try {
				return $this->hydrate($value, $type, $hydrationPath);
			} catch (HydrationException $ex) {
				$exceptions[] = $ex;
			}
		}
		throw $exceptions[0];
	}

	private function hydrateAlias(Value $value, AliasType $targetType, string $hydrationPath): Value {
		return $this->hydrate($value, $targetType->aliasedType(), $hydrationPath);
	}

	private function hydrateSubtype(Value $value, SubtypeType $targetType, string $hydrationPath): SubtypeValue {
		$cast = $this->castRegistry->getCast(
			new TypeNameIdentifier('JsonValue'),
			$targetType->typeName()
		);
		if ($cast !== NoCastAvailable::value) {
			try {
				/** @var SubtypeValue */
				return $this->functionExecutor->executeCastFunction(
					$this->subExpressionExecutor,
					$cast,
					$value
				);
			} catch (ThrowResult $throwResult) {
				throw new HydrationException(
					$value,
					$hydrationPath,
					sprintf("Subtype hydration failed: %s",
						$throwResult->v
					)
				);
			}
		}

		$baseValue = $this->hydrate($value, $targetType->baseType(), $hydrationPath);

		try {
			$this->functionExecutor->executeConstructor(
				$this->subExpressionExecutor,
				$targetType,
				$baseValue
			);
			return new SubtypeValue(
				$targetType->typeName(),
				$baseValue
			);
		} catch (ThrowResult $throwResult) {
			throw new HydrationException(
				$value,
				$hydrationPath,
				sprintf("Subtype hydration failed: %s",
					$throwResult->v
				)
			);
		}
	}

	private function hydrateMutable(Value $value, MutableType $targetType, string $hydrationPath): MutableValue {
		return new MutableValue(
			$targetType->refType,
			$this->hydrate($value, $targetType->refType, $hydrationPath)
		);
	}

	private function hydrateInteger(Value $value, IntegerType $targetType, string $hydrationPath): LiteralValue {
		if ($value instanceof LiteralValue && $value->literal instanceof IntegerLiteral) {
			if ((
				$targetType->range->minValue === MinusInfinity::value ||
				$targetType->range->minValue <= $value->literal->value
			) && (
					$targetType->range->maxValue === PlusInfinity::value ||
					$targetType->range->maxValue >= $value->literal->value
			)) {
				return $value;
			}
			throw new HydrationException(
				$value,
				$hydrationPath,
				sprintf("The integer value should be in the range %s..%s",
					$targetType->range->minValue === MinusInfinity::value ? "-Infinity" : $targetType->range->minValue,
					$targetType->range->maxValue === PlusInfinity::value ? "+Infinity" : $targetType->range->maxValue,
				)
			);
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			sprintf("The value should be an integer in the range %s..%s",
				$targetType->range->minValue === MinusInfinity::value ? "-Infinity" : $targetType->range->minValue,
				$targetType->range->maxValue === PlusInfinity::value ? "+Infinity" : $targetType->range->maxValue,
			)
		);
	}

	private function hydrateBoolean(Value $value, BooleanType $targetType, string $hydrationPath): LiteralValue {
		if ($value instanceof LiteralValue && $value->literal instanceof BooleanLiteral) {
			return $value;
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			"The value should be a boolean"
		);
	}

	private function hydrateNull(Value $value, NullType $targetType, string $hydrationPath): LiteralValue {
		if ($value instanceof LiteralValue && $value->literal instanceof NullLiteral) {
			return $value;
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			"The value should be 'null'"
		);
	}

	private function hydrateTrue(Value $value, TrueType $targetType, string $hydrationPath): LiteralValue {
		if ($value instanceof LiteralValue && $value->literal instanceof BooleanLiteral) {
			if ($value->literal->value === true) {
				return $value;
			}
			throw new HydrationException(
				$value,
				$hydrationPath,
				"The boolean value should be true"
			);
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			"The value should be 'true'"
		);
	}

	private function hydrateFalse(Value $value, FalseType $targetType, string $hydrationPath): LiteralValue {
		if ($value instanceof LiteralValue && $value->literal instanceof BooleanLiteral) {
			if ($value->literal->value === false) {
				return $value;
			}
			throw new HydrationException(
				$value,
				$hydrationPath,
				"The boolean value should be false"
			);
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			"The value should be 'false'"
		);
	}

	private function hydrateString(Value $value, StringType $targetType, string $hydrationPath): LiteralValue {
		if ($value instanceof LiteralValue && $value->literal instanceof StringLiteral) {
			$l = mb_strlen($value->literal->value);
			if ($targetType->range->minLength <= $l && (
					$targetType->range->maxLength === PlusInfinity::value ||
					$targetType->range->maxLength >= $l
			)) {
				return $value;
			}
			throw new HydrationException(
				$value,
				$hydrationPath,
				sprintf("The string value should be with a length between %s and %s",
					$targetType->range->minLength,
					$targetType->range->maxLength === PlusInfinity::value ? "+Infinity" : $targetType->range->maxLength,
				)
			);
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			sprintf("The value should a string with a length between %s and %s",
				$targetType->range->minLength,
				$targetType->range->maxLength === PlusInfinity::value ? "+Infinity" : $targetType->range->maxLength,
			)
		);
	}

	private function hydrateArray(Value $value, ArrayType $targetType, string $hydrationPath): ListValue {
		if ($value instanceof ListValue) {
			$l = count($value->items);
			if ($targetType->range->minLength <= $l && (
					$targetType->range->maxLength === PlusInfinity::value ||
					$targetType->range->maxLength >= $l
			)) {
				$refType = $targetType->itemType;
				$result = [];
				foreach($value->items as $seq => $item) {
					$result[] = $this->hydrate($item, $refType, "{$hydrationPath}[$seq]");
				}
				return new ListValue(... $result);
			}
			throw new HydrationException(
				$value,
				$hydrationPath,
				sprintf("The array value should be with a length between %s and %s",
					$targetType->range->minLength,
					$targetType->range->maxLength === PlusInfinity::value ? "+Infinity" : $targetType->range->maxLength,
				)
			);
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			sprintf("The value should be an array with a length between %s and %s",
				$targetType->range->minLength,
				$targetType->range->maxLength === PlusInfinity::value ? "+Infinity" : $targetType->range->maxLength,
			)
		);
	}

	private function hydrateTuple(Value $value, TupleType $targetType, string $hydrationPath): ListValue {
		if ($value instanceof ListValue) {
			$l = count($value->items);
			if (count($targetType->types) <= $l) {
				$result = [];
				foreach($targetType->types as $seq => $refType) {
					$item = $value->items[$seq];
					$result[] = $this->hydrate($item, $refType, "{$hydrationPath}[$seq]");
				}
				return new ListValue(... $result);
			}
			throw new HydrationException(
				$value,
				$hydrationPath,
				sprintf("The tuple value should be with %s items",
					count($targetType->types),
				)
			);
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			sprintf("The value should be a tuple with %d items",
				count($targetType->types),
			)
		);
	}

	private function hydrateMap(Value $value, MapType $targetType, string $hydrationPath): DictValue {
		if ($value instanceof DictValue) {
			$l = count($value->items);
			if ($targetType->range->minLength <= $l && (
					$targetType->range->maxLength === PlusInfinity::value ||
					$targetType->range->maxLength >= $l
			)) {
				$refType = $targetType->itemType;
				$result = [];
				foreach($value->items as $key => $item) {
					$result[$key] = $this->hydrate($item, $refType, "{$hydrationPath}.$key");
				}
				return new DictValue($result);
			}
			throw new HydrationException(
				$value,
				$hydrationPath,
				sprintf("The map value should be with a length between %s and %s",
					$targetType->range->minLength,
					$targetType->range->maxLength === PlusInfinity::value ? "+Infinity" : $targetType->range->maxLength,
				)
			);
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			sprintf("The value should be a map with a length between %s and %s",
				$targetType->range->minLength,
				$targetType->range->maxLength === PlusInfinity::value ? "+Infinity" : $targetType->range->maxLength,
			)
		);
	}

	private function hydrateRecord(Value $value, RecordType $targetType, string $hydrationPath): DictValue {
		if ($value instanceof DictValue) {
			$l = count($value->items);
			if (count($targetType->types) <= $l) {
				$result = [];
				foreach($targetType->types as $key => $refType) {
					$item = $value->items[$key] ?? throw new HydrationException(
						$value,
						$hydrationPath,
						sprintf("The record value should contain the key %s", $key)
					);
					$result[$key] = $this->hydrate($item, $refType, "{$hydrationPath}.$key");
				}
				return new DictValue( $result);
			}
			throw new HydrationException(
				$value,
				$hydrationPath,
				sprintf("The record value should be with %s items",
					count($targetType->types),
				)
			);
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			sprintf("The value should be a record with %d items",
				count($targetType->types),
			)
		);
	}

	private function hydrateReal(Value $value, RealType $targetType, string $hydrationPath): LiteralValue {
		if ($value instanceof LiteralValue && (
			$value->literal instanceof IntegerLiteral || $value->literal instanceof RealLiteral
		)) {
			if ((
				$targetType->range->minValue === MinusInfinity::value ||
				$targetType->range->minValue <= $value->literal->value
			) && (
					$targetType->range->maxValue === PlusInfinity::value ||
					$targetType->range->maxValue >= $value->literal->value
			)) {
				return new LiteralValue(new RealLiteral((float)$value->literal->value));
			}
			throw new HydrationException(
				$value,
				$hydrationPath,
				sprintf("The real value should be in the range %s..%s",
					$targetType->range->minValue === MinusInfinity::value ? "-Infinity" : $targetType->range->minValue,
					$targetType->range->maxValue === PlusInfinity::value ? "+Infinity" : $targetType->range->maxValue,
				)
			);
		}
		throw new HydrationException(
			$value,
			$hydrationPath,
			sprintf("The value should a real number in the range %s..%s",
				$targetType->range->minValue === MinusInfinity::value ? "-Infinity" : $targetType->range->minValue,
				$targetType->range->maxValue === PlusInfinity::value ? "+Infinity" : $targetType->range->maxValue,
			)
		);
	}

}