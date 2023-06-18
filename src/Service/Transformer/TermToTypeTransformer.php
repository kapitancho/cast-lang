<?php

namespace Cast\Service\Transformer;

use Cast\Model\Program\Type\ErrorTypeTerm;
use Cast\Model\Program\Type\FunctionReturnTypeTerm;
use Cast\Model\Program\Type\TypeTerm;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\Type;
use Cast\Service\Type\InvalidTypeTerm;

interface TermToTypeTransformer {
	public function functionReturnTermToType(FunctionReturnTypeTerm $term): FunctionReturnType;
	public function termToErrorType(ErrorTypeTerm $errorTypeTerm): ErrorType;
	/** @throws InvalidTypeTerm */
	public function termToType(TypeTerm $term): Type;
}