<?php

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Registry\NoCastAvailable;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @template-implements ByTypeMethodFinder<SubtypeType>
 */
final readonly class SubtypeMethodFinder implements ByTypeMethodFinder {
	use MethodFinderHelper;

	public function __construct(
		private TypeAssistant $typeAssistant,
		private CastRegistry $castRegistry,
		private SubtypeRelationChecker $subtypeRelationChecker
	) {}

	private function typeRef(TypeNameIdentifier|string $name): Type {
		return $this->typeAssistant->typeByName($name instanceof TypeNameIdentifier ?
			$name : new TypeNameIdentifier($name)
		);
	}

	/**
	 * @param Type $originalTargetType
	 * @param SubtypeType $targetType
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
		if ($targetType->typeName()->identifier === 'Container') {
			if ($parameterType instanceof TypeType) {
				if ($methodName->identifier === 'instanceOf') {
					return $this->getFunction("Container::instanceOf",
						$parameterType->refType,
						new ErrorType(errorType: $this->typeRef('ContainerError'))
					);
				}
			}
		}
		if ($targetType->typeName()->identifier === 'DatabaseConnection') {
			if ($methodName->identifier === 'execute' || $methodName->identifier === 'query') {
				if(
					$parameterType instanceof RecordType &&
					array_key_exists('query', $parameterType->types) &&
					array_key_exists('boundParameters', $parameterType->types) &&
					$parameterType->types['query'] instanceof StringType
				) {
					$t = $parameterType->types['boundParameters'];
					$t = $this->typeAssistant->followAliases($t);
					if ($t instanceof TupleType) {
						$t = new ArrayType(
							(new UnionTypeNormalizer(new SubtypeRelationChecker))->normalize(... $t->types),
							new LengthRange($len = count($t->types), $len)
						);
					}
					if ($t instanceof RecordType) {
						$t = new MapType(
							(new UnionTypeNormalizer(new SubtypeRelationChecker))->normalize(... $t->types),
							new LengthRange($len = count($t->types), $len)
						);
					}
					if (
						($t instanceof ArrayType || $t instanceof MapType) &&
						$this->subtypeRelationChecker->isSubtype(
							$t->itemType,
							new UnionType(
								IntegerType::base(), StringType::base(), new NullType
							)
						)
					) {
						$returnType = $methodName->identifier === 'query' ?
							$this->typeRef('QueryResult')
							/*new ArrayType(
								new MapType(new UnionType(
									IntegerType::base(), StringType::base(), new NullType
								), new LengthRange(0, PlusInfinity::value)),
								new LengthRange(0, PlusInfinity::value)
							)*/ :
							new IntegerType(new IntegerRange(0, PlusInfinity::value));
						return $this->getFunction(
							"DatabaseConnection::" . $methodName->identifier,
							$returnType,
							new ErrorType(
								errorType: $this->typeRef('QueryFailure')
							)
						);
					}
				}
			}
		}
		if ($methodName->identifier === 'baseValue') {
			return $this->getFunction("Subtype::baseValue",
				$targetType->baseType()
			);
		}
		if ($methodName->identifier === 'with') {
			if ($parameterType instanceof RecordType && $targetType->baseType() instanceof RecordType) {
				return $this->getFunction("Subtype::with",
					$this->typeRef($targetType->typeName()),
					$targetType->errorType()
				);
			}
		}
		if ($methodName->identifier === 'asText') {
			$cast = $this->castRegistry->getCast(
				$targetType->typeName(),
				new TypeNameIdentifier('String')
			);
			if ($cast !== NoCastAvailable::value) {
				return $this->getFunction("Subtype::asText", StringType::base());
			}
		}
		if ($methodName->identifier === 'invoke') {
			$cast = $this->castRegistry->getCast($targetType->typeName(), new TypeNameIdentifier("Invokable"));
			if ($cast !== NoCastAvailable::value) {
				$fnBody = $cast->functionBody->body;
				if ($fnBody instanceof ConstantExpression) { //todo sequence, etc.
					$fnVal = $fnBody->constant;
					if ($fnVal instanceof FunctionValue) {
						if ($this->subtypeRelationChecker->isSubtype(
							$parameterType,
							$fnVal->parameterType
						)) {
							return $this->getFunction("Subtype::invoke",
								$fnVal->returnType->returnType,
								$fnVal->returnType->errorType,
							);
						}
					}
				}
			}
		}

		return NoMethodAvailable::value;
	}
}