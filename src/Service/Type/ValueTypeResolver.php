<?php

namespace Cast\Service\Type;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\Literal\RealLiteral;
use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\EnumerationValueType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\FunctionType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Value\ClosureValue;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Transformer\TypeRegistry;

final readonly class ValueTypeResolver {

	public function __construct(
		private TypeRegistry $typeByNameFinder
	) {}

	private function findTypeOfLiteralValue(LiteralValue $source): Type {
		$literal = $source->literal;
		return match($literal::class) {
			IntegerLiteral::class => new IntegerType(
				new IntegerRange($literal->value, $literal->value)
			),
			RealLiteral::class => new RealType(
				new RealRange($literal->value, $literal->value)
			),
			BooleanLiteral::class => $literal->value ? new TrueType : new FalseType,
			NullLiteral::class => new NullType,
			StringLiteral::class => new StringType(
				new LengthRange($l = strlen($literal->value), $l)
			)
		};
	}

	/**
	 * @param Value[] $items
	 * @return Type[]
	 */
	private function findItemsType(array $items): array {
		return array_map(
			fn(Value $itemValue): Type => $this->findTypeOf($itemValue),
			$items
		);
	}

	private function getEnumerationType(TypeNameIdentifier $name): EnumerationType {
		$result = $this->typeByNameFinder->typeByName($name);
		return $result instanceof EnumerationType ? $result :
			UnknownType::withName($name);
	}

	private function getSubtypeType(TypeNameIdentifier $name): SubtypeType {
		$result = $this->typeByNameFinder->typeByName($name);
		return $result instanceof SubtypeType ? $result :
			UnknownType::withName($name);
	}

	private function getTypeType(Type $type): Type {
		if ($type instanceof TupleType) {
			return new IntersectionType(
				$type,
				new TupleType(...
					array_map(fn(Type $type): TypeType
						=> new TypeType($type), $type->types)
				),
				new TypeType($type)
			);
		}
		return new TypeType($type);
	}

	/** @throws InvalidValue */
	public function findTypeOf(Value $source): Type {
		return match($source::class) {
			ClosureValue::class => $this->findTypeOf($source->function),
			DictValue::class => new RecordType(... $this->findItemsType($source->items)),
			EnumerationValue::class => new EnumerationValueType(
				$this->getEnumerationType($source->enumTypeName),
				$source->enumValue
			),
			FunctionValue::class => new FunctionType(
				$source->parameterType,
				$source->returnType,
			),
			ListValue::class => new TupleType(... $this->findItemsType($source->items)),
			LiteralValue::class => $this->findTypeOfLiteralValue($source),
			SubtypeValue::class => $this->getSubtypeType($source->typeName),
			TypeValue::class => $this->getTypeType($source->type),
			default => InvalidValue::of($source)
		};
	}

}