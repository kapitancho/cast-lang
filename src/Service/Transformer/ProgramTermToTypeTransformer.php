<?php

namespace Cast\Service\Transformer;

use Cast\Model\Program\Type\AnyTypeTerm;
use Cast\Model\Program\Type\ArrayTypeTerm;
use Cast\Model\Program\Type\BooleanTypeTerm;
use Cast\Model\Program\Type\EnumerationValueTypeTerm;
use Cast\Model\Program\Type\ErrorTypeTerm;
use Cast\Model\Program\Type\FalseTypeTerm;
use Cast\Model\Program\Type\FunctionReturnTypeTerm;
use Cast\Model\Program\Type\FunctionTypeTerm;
use Cast\Model\Program\Type\IntegerTypeTerm;
use Cast\Model\Program\Type\IntersectionTypeTerm;
use Cast\Model\Program\Type\MapTypeTerm;
use Cast\Model\Program\Type\MutableTypeTerm;
use Cast\Model\Program\Type\NothingTypeTerm;
use Cast\Model\Program\Type\NullTypeTerm;
use Cast\Model\Program\Type\RealTypeTerm;
use Cast\Model\Program\Type\RecordTypeTerm;
use Cast\Model\Program\Type\StringTypeTerm;
use Cast\Model\Program\Type\TrueTypeTerm;
use Cast\Model\Program\Type\TupleTypeTerm;
use Cast\Model\Program\Type\TypeNameTerm;
use Cast\Model\Program\Type\TypeTerm;
use Cast\Model\Program\Type\TypeTypeTerm;
use Cast\Model\Program\Type\UnionTypeTerm;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\EnumerationValueType;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FalseType;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\FunctionType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\MutableType;
use Cast\Model\Runtime\Type\NothingType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\TrueType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Service\Type\InvalidTypeTerm;

final readonly class ProgramTermToTypeTransformer implements TermToTypeTransformer {

	public function __construct(
		private TypeRegistry $typeByNameFinder,
	) {}

	/** @throws InvalidTypeTerm */
	private function convertFunctionType(FunctionTypeTerm $term): FunctionType {
		return new FunctionType(
			$this->termToType($term->parameterType),
			$this->functionReturnTermToType($term->returnType),
		);
	}

	/** @throws InvalidTypeTerm */
	private function convertRecordType(RecordTypeTerm $recordType): RecordType {
		$result = [];
		foreach($recordType->types as $key => $type) {
			$result[$key] = $this->termToType($type);
		}
		return new RecordType(... $result);
	}

	/** @throws InvalidTypeTerm */
	private function convertTupleType(TupleTypeTerm $tupleType): TupleType {
		$result = [];
		foreach($tupleType->types as $type) {
			$result[] = $this->termToType($type);
		}
		return new TupleType(... $result);
	}

	/** @throws InvalidTypeTerm */
	private function convertIntersectionType(IntersectionTypeTerm $tupleType): IntersectionType {
		$result = [];
		foreach($tupleType->types as $type) {
			$result[] = $this->termToType($type);
		}
		return new IntersectionType(... $result);
	}

	/** @throws InvalidTypeTerm */
	private function convertUnionType(UnionTypeTerm $tupleType): UnionType {
		$result = [];
		foreach($tupleType->types as $type) {
			$result[] = $this->termToType($type);
		}
		return new UnionType(... $result);
	}

	private function convertTypeNameTerm(TypeNameTerm $term): Type {
		return $this->typeByNameFinder->typeByName($term->typeName);
	}

	private function convertEnumerationValueTypeTerm(EnumerationValueTypeTerm $term): EnumerationValueType {
		$typeDefinition = $this->typeByNameFinder->typeByName($term->enumTypeName);
		return $typeDefinition instanceof EnumerationType ?
			new EnumerationValueType($typeDefinition,$term->enumValue) :
			throw InvalidTypeTerm::of($term);
	}

	public function functionReturnTermToType(FunctionReturnTypeTerm $term): FunctionReturnType {
		return new FunctionReturnType(
			$this->termToType($term->returnType),
			$this->termToErrorType($term->errorType)
		);
	}

	public function termToErrorType(ErrorTypeTerm $errorTypeTerm): ErrorType {
		return new ErrorType(
			$errorTypeTerm->anyError,
			$this->termToType($errorTypeTerm->errorType)
		);
	}

	/** @throws InvalidTypeTerm */
	public function termToType(TypeTerm $term): Type {
		return match($term::class) {
			ArrayTypeTerm::class => new ArrayType(
				$this->termToType($term->itemType),
				$term->range
			),
			MapTypeTerm::class => new MapType(
				$this->termToType($term->itemType),
				$term->range
			),
			FunctionTypeTerm::class => $this->convertFunctionType($term),
			EnumerationValueTypeTerm::class => $this->convertEnumerationValueTypeTerm($term),
			TypeTypeTerm::class => new TypeType($this->termToType($term->refType)),
			MutableTypeTerm::class => new MutableType($this->termToType($term->refType)),
			IntersectionTypeTerm::class => $this->convertIntersectionType($term),
			UnionTypeTerm::class => $this->convertUnionType($term),
			RecordTypeTerm::class => $this->convertRecordType($term),
			TupleTypeTerm::class => $this->convertTupleType($term),
			AnyTypeTerm::class => new AnyType,
			NothingTypeTerm::class => new NothingType,
			IntegerTypeTerm::class => new IntegerType($term->range),
			RealTypeTerm::class => new RealType($term->range),
			StringTypeTerm::class => new StringType($term->range),
			BooleanTypeTerm::class => new BooleanType,
			TrueTypeTerm::class => new TrueType,
			FalseTypeTerm::class => new FalseType,
			NullTypeTerm::class => new NullType,

			TypeNameTerm::class => $this->convertTypeNameTerm($term),

			default => throw InvalidTypeTerm::of($term)
		};
	}

}