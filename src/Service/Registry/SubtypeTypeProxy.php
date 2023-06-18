<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Type\ErrorTypeTerm;
use Cast\Model\Program\Type\TypeTerm;
use Cast\Model\Runtime\Type\ErrorType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\Type;
use Cast\Service\Transformer\TermToTypeTransformer;

final readonly class SubtypeTypeProxy implements SubtypeType {
	public function __construct(
		private TermToTypeTransformer $termToTypeTransformer,
		private TypeNameIdentifier $typeName,
		private ErrorTypeTerm $errorType,
		private FunctionBodyExpression $functionBody,
		private TypeTerm $baseType
	) {}

	public function typeName(): TypeNameIdentifier {
		return $this->typeName;
	}
	public function baseType(): Type {
		return $this->termToTypeTransformer->termToType($this->baseType);
	}
	public function functionBody(): FunctionBodyExpression {
		return $this->functionBody;
	}
	public function errorType(): ErrorType {
		return $this->termToTypeTransformer->termToErrorType($this->errorType);
	}

	public function __toString(): string {
		$body = sprintf(" :: %s", $this->functionBody);
		if ($body === ' :: {null}') {
			$body = '';
		}
		if ($body === ' :: null') {
			$body = '';
		}
		$errorType = (string)$this->errorType;
		if ($errorType !== '') {
			$errorType = ' ' . $errorType;
		}
		return sprintf($this->typeName);
		/*return sprintf("%s <: %s%s%s",
			$this->typeName, $this->baseType, $errorType, $body);*/
	}

}