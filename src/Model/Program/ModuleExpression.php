<?php

namespace Cast\Model\Program;

use Cast\Model\Program\Term\Expression\ConstantAssignmentTerm;
use Cast\Model\Program\Term\Expression\TypeCastTerm;
use Cast\Model\Program\Term\Expression\TypeDefinitionTerm;

final readonly class ModuleExpression implements ProgramNode {
	public function __construct(
		public TypeDefinitionTerm|ConstantAssignmentTerm|TypeCastTerm
			$expression
	) {}

	public function __toString(): string {
		return sprintf("%s;\n", $this->expression);
	}

	public function jsonSerialize(): TypeDefinitionTerm|ConstantAssignmentTerm|TypeCastTerm {
		return $this->expression;
	}
}