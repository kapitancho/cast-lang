<?php

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Runtime\Type\AliasType;
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
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Registry\NoCastAvailable;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\UnionTypeNormalizer;

/**
 * @template-implements ByTypeMethodFinder<AliasType>
 */
final readonly class AliasMethodFinder implements ByTypeMethodFinder {
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
	 * @param AliasType $targetType
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
		if ($methodName->identifier === 'asText') {
			$cast = $this->castRegistry->getCast(
				$targetType->typeName(),
				new TypeNameIdentifier('String')
			);
			if ($cast !== NoCastAvailable::value) {
				return $this->getFunction(
					"Alias::asText",
					StringType::base(),
					parameterType: $targetType
				);
			}
		}
		return NoMethodAvailable::value;
	}
}