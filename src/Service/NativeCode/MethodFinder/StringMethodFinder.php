<?php /** @noinspection NestedPositiveIfStatementsInspection */

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @template-implements ByTypeMethodFinder<StringType>
 */
final readonly class StringMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	public function __construct(
		private TypeAssistant $typeAssistant,
		private UnionTypeNormalizer $unionTypeNormalizer,
		private SubtypeRelationChecker $subtypeRelationChecker,
	) {}

	private function typeRef(string $name): Type {
		return $this->typeAssistant->typeByName(new TypeNameIdentifier($name));
	}

	/**
	 * @param Type $originalTargetType
	 * @param StringType $targetType
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
		if ($methodName->identifier === 'PRINT') {
			return $this->getFunction("PRINT", StringType::base());
		}
		if ($methodName->identifier === 'jsonStringToValue') {
			return $this->getFunction("String::jsonStringToValue",
				$this->typeAssistant->typeByName(new TypeNameIdentifier("JsonValue")),
				new ErrorType(
					errorType: $this->typeAssistant->typeByName(
						new TypeNameIdentifier("InvalidJsonValue")),
				)
			);
		}
		if ($methodName->identifier === 'hydrateJsonAs') {
			if ($parameterType instanceof TypeType) {
				if ($this->subtypeRelationChecker->isSubtype(
					$originalTargetType, StringType::base())
				) {
					return $this->getFunction("String::hydrateJsonAs",
						$parameterType->refType,
						new ErrorType(errorType: $this->typeRef('HydrationFailed'))
					);
				}
			}
		}

		if ($methodName->identifier === 'asIntegerNumber') {
			return $this->getFunction("String::asIntegerNumber", IntegerType::base(),
				new ErrorType(errorType: $this->typeAssistant->typeByName(new TypeNameIdentifier('StringIsNoIntegerNumber')))
			);
		}
		if ($methodName->identifier === 'asRealNumber') {
			return $this->getFunction("String::asRealNumber", RealType::base(),
				new ErrorType(errorType: $this->typeAssistant->typeByName(new TypeNameIdentifier('StringIsNoRealNumber')))
			);
		}
		if ($methodName->identifier === 'length') {
			return $this->getFunction("String::length", new IntegerType(
				new IntegerRange(
					$targetType->range->minLength,
					$targetType->range->maxLength,
				),
			));
		}
		if ($methodName->identifier === 'reverse') {
			return $this->getFunction("String::reverse", $targetType);
		}
		if ($methodName->identifier === 'toLowerCase') {
			return $this->getFunction("String::toLowerCase", $targetType);
		}
		if ($methodName->identifier === 'toUpperCase') {
			return $this->getFunction("String::toUpperCase", $targetType);
		}
		if ($methodName->identifier === 'trim') {
			return $this->getFunction("String::trim", new StringType(
				new LengthRange(
					0,
					$targetType->range->maxLength,
				),
			));
		}
		if ($methodName->identifier === 'trimLeft') {
			return $this->getFunction("String::trimLeft", new StringType(
				new LengthRange(
					0,
					$targetType->range->maxLength,
				),
			));
		}
		if ($methodName->identifier === 'trimRight') {
			return $this->getFunction("String::trimRight", new StringType(
				new LengthRange(
					0,
					$targetType->range->maxLength,
				),
			));
		}
		if ($methodName->identifier === 'concatList') {
			if ($this->subtypeRelationChecker->isSubtype($parameterType,
				new ArrayType(StringType::base(),
					new LengthRange(0, PlusInfinity::value))
			)) {
				$min = $targetType->range->minLength;
				$max = PlusInfinity::value;
				if ($parameterType instanceof TupleType) {
					$max = $targetType->range->maxLength;
					foreach ($parameterType->types as $paramType) {
						if ($paramType instanceof StringType) {
							$min += $paramType->range->minLength;
							$max = $max === PlusInfinity::value ||
								$paramType->range->minLength === PlusInfinity::value ? PlusInfinity::value :
								$max + $paramType->range->maxLength;
						}
					}
				} elseif ($parameterType instanceof ArrayType) {
					if ($parameterType->itemType instanceof StringType) {
						$min += $parameterType->range->minLength * $parameterType->itemType->range->minLength;
						$aL = $parameterType->range->minLength;
						$sL = $parameterType->itemType->range->minLength;
						if ($aL !== PlusInfinity::value && $sL !== PlusInfinity::value) {
							$max = $targetType->range->maxLength + $aL * $sL;
						}
					} //todo - subtypes
				}
				return $this->getFunction("String::concatList", new StringType(
					new LengthRange(
						$min, $max
					),
				));
			}
		}
		if ($this->subtypeRelationChecker->isSubtype($parameterType, StringType::base())) {
			$p = match(true) {
				$parameterType instanceof UnionType =>
					$this->unionTypeNormalizer->toBaseType(...$parameterType->types),
				$parameterType instanceof StringType => $parameterType,
				default => StringType::base()
			};
			if ($methodName->identifier === 'concat' || $methodName->identifier === 'binaryPlus') {
				return $this->getFunction("String::concat", new StringType(
					new LengthRange(
						$targetType->range->minLength + $p->range->minLength,
						is_int($targetType->range->maxLength) &&
						is_int($p->range->maxLength) ?
							$targetType->range->maxLength + $p->range->maxLength :
							PlusInfinity::value,
					),
				));
			}
			if ($methodName->identifier === 'contains') {
				return $this->getFunction("String::contains", new BooleanType);
			}
			if ($methodName->identifier === 'startsWith') {
				return $this->getFunction("String::startsWith", new BooleanType);
			}
			if ($methodName->identifier === 'endsWith') {
				return $this->getFunction("String::endsWith", new BooleanType);
			}
			if ($methodName->identifier === 'positionOf') {
				return $this->getFunction("String::positionOf",
					new IntegerType(
						new IntegerRange(0,
							$targetType->range->maxLength === PlusInfinity::value ?
								PlusInfinity::value : $targetType->range->maxLength - 1
						)
					),
					new ErrorType(
						errorType: $this->typeAssistant->typeByName(
							new TypeNameIdentifier("SubstringNotInString")
						)
					)
				);
			}
			if ($methodName->identifier === 'lastPositionOf') {
				return $this->getFunction("String::lastPositionOf",
					new IntegerType(
						new IntegerRange(0,
							$targetType->range->maxLength === PlusInfinity::value ?
								PlusInfinity::value : $targetType->range->maxLength - 1
						)
					),
					new ErrorType(
						errorType: $this->typeAssistant->typeByName(
							new TypeNameIdentifier("SubstringNotInString")
						)
					)
				);
			}
			if ($methodName->identifier === 'split') {
				if ($p->range->minLength > 0) {
					return $this->getFunction("String::split", new ArrayType(
						$targetType,
						new LengthRange(0, $targetType->range->maxLength)
					));
				}
			}
		}
		if ($parameterType instanceof IntegerType) {
			if ($methodName->identifier === 'chunk') {
				if (($minVal = $parameterType->range->minValue) > 0) {
					return $this->getFunction("String::chunk", new ArrayType(
						new StringType(
							new LengthRange(
								$minVal,
								$parameterType->range->maxValue
							)
						),
						new LengthRange(0, $targetType->range->maxLength)
					));
				}
			}
		}
		if ($parameterType instanceof RecordType) { //TODO - with for Record
			if (($methodName->identifier === 'padLeft' || $methodName->identifier === 'padRight') &&
				array_key_exists('length', $parameterType->types) &&
				array_key_exists('padString', $parameterType->types) &&
				$parameterType->types['length'] instanceof IntegerType &&
				$parameterType->types['length']->range->minValue > 0 &&
				$parameterType->types['padString'] instanceof StringType &&
				$parameterType->types['padString']->range->minLength > 0
			) {
				return $this->getFunction("String::" . $methodName->identifier, new StringType(
					new LengthRange(
						max(
							$targetType->range->minLength,
							$parameterType->types['length']->range->minValue
						),
						max(
							$targetType->range->maxLength,
							$parameterType->types['length']->range->maxValue
						)
					)
				));
			}
			if ($methodName->identifier === 'substring' &&
				array_key_exists('start', $parameterType->types) &&
				$parameterType->types['start'] instanceof IntegerType &&
				$parameterType->types['start']->range->minValue >= 0
			) {
				if (array_key_exists('end', $parameterType->types) &&
					$parameterType->types['end'] instanceof IntegerType &&
					$parameterType->types['end']->range->minValue >= 0
				) {
					return $this->getFunction("String::substringTo", $targetType);
				}
				if (array_key_exists('length', $parameterType->types) &&
					$parameterType->types['length'] instanceof IntegerType &&
					$parameterType->types['length']->range->minValue > 0
				) {
					return $this->getFunction("String::substringLength", new StringType(
						new LengthRange(
							min(
								$targetType->range->minLength,
								$parameterType->types['length']->range->maxValue
							),
							$parameterType->types['length']->range->maxValue === PlusInfinity::value ?
								$targetType->range->maxLength : min(
									$targetType->range->maxLength,
									$parameterType->types['length']->range->maxValue
								),
						)
					));
				}
			}
		}
		return NoMethodAvailable::value;
	}
}