<?php

namespace Cast\Service\Transformer;

use Cast\Model\Program\Constant\Constant;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Program;
use Cast\Model\Program\Term\Term;
use Cast\Model\Program\Type\ErrorTypeTerm;
use Cast\Model\Program\Type\FunctionReturnTypeTerm;
use Cast\Model\Program\Type\TypeTerm;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Registry\BuiltInTypeRegistry;
use Cast\Service\Value\ProgramConstantToValueConverter;

final readonly class ProgramTransformer implements ConstantToValueConverter, TermToExpressionConverter, TermToTypeTransformer, TypeRegistry {

	private ConstantToValueConverter $constantToValueConverter;
	private TermToExpressionConverter $termToExpressionConverter;
	private TermToTypeTransformer $termToTypeTransformer;
	private TypeRegistry $typeByNameFinder;

	public function __construct(
		Program $program,
		BuiltInTypeRegistry $builtInTypeRegistry
	) {
		$this->constantToValueConverter = new ProgramConstantToValueConverter(
			$this, $this
		);
		$this->termToExpressionConverter = new ProgramTermToExpressionConverter(
			$this, $this,
		);
		$this->termToTypeTransformer = new ProgramTermToTypeTransformer(
			$this
		);
		$this->typeByNameFinder = new ProgramTypeRegistry(
			$program,
			$builtInTypeRegistry,
			$this,
			$this
		);
	}

	public function convertConstantToValue(Constant $constant): Value {
		return $this->constantToValueConverter->convertConstantToValue($constant);
	}

	public function termToExpression(Term $term): Expression {
		return $this->termToExpressionConverter->termToExpression($term);
	}

	public function functionReturnTermToType(FunctionReturnTypeTerm $term): FunctionReturnType {
		return $this->termToTypeTransformer->functionReturnTermToType($term);
	}

	public function termToErrorType(ErrorTypeTerm $errorTypeTerm): ErrorType {
		return $this->termToTypeTransformer->termToErrorType($errorTypeTerm);
	}

	public function termToType(TypeTerm $term): Type {
		return $this->termToTypeTransformer->termToType($term);
	}

	/** @return Type[] */
	public function allTypes(): array {
		return $this->typeByNameFinder->allTypes();
	}

	public function typeByName(TypeNameIdentifier $name): Type {
		return $this->typeByNameFinder->typeByName($name);
	}
}