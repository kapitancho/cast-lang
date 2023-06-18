<?php /** @noinspection NestedTernaryOperatorInspection */

namespace Cast\Service\Program;

use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\ModuleComment;
use Cast\Model\Program\ModuleExpression;
use Cast\Model\Program\Node;
use Cast\Model\Program\Program;
use Cast\Model\Program\ProgramModule;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\MinusInfinity;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Range\RealRange;
use Cast\Model\Program\Term\BinaryTerm;
use Cast\Model\Program\Term\CatchTerm;
use Cast\Model\Program\Term\ConstantTerm;
use Cast\Model\Program\Term\ConstructorCallTerm;
use Cast\Model\Program\Term\DefaultMatchTerm;
use Cast\Model\Program\Term\Expression\ConstantAssignmentTerm;
use Cast\Model\Program\Term\Expression\TypeCastTerm;
use Cast\Model\Program\Term\Expression\TypeDefinitionTerm;
use Cast\Model\Program\Term\FunctionBodyTerm;
use Cast\Model\Program\Term\FunctionCallTerm;
use Cast\Model\Program\Term\LoopTerm;
use Cast\Model\Program\Term\MatchIfTerm;
use Cast\Model\Program\Term\MatchPairTerm;
use Cast\Model\Program\Term\MatchTrueTerm;
use Cast\Model\Program\Term\MatchTypePairTerm;
use Cast\Model\Program\Term\MatchValueTerm;
use Cast\Model\Program\Term\MethodCallTerm;
use Cast\Model\Program\Term\PropertyAccessTerm;
use Cast\Model\Program\Term\RecordTerm;
use Cast\Model\Program\Term\ReturnTerm;
use Cast\Model\Program\Term\SequenceTerm;
use Cast\Model\Program\Term\Term;
use Cast\Model\Program\Term\ThrowTerm;
use Cast\Model\Program\Term\TupleTerm;
use Cast\Model\Program\Term\UnaryTerm;
use Cast\Model\Program\Term\VariableAssignmentTerm;
use Cast\Model\Program\Term\VariableNameTerm;
use Cast\Model\Runtime\Type\AliasType;
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
use Cast\Model\Runtime\Type\NamedType;
use Cast\Model\Runtime\Type\NothingType;
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
use Cast\Model\Runtime\Value\ClosureValue;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\MutableValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Model\Runtime\Value\Value;

final class ValueHtmlPrinter {
	public function print(Value $value): string {
		return $this->printNode($value);
	}

	private function printNode(Node|Type|Value|FunctionReturnType|FunctionBodyExpression $value): string {
		return match($value::class) {
			Program::class => $this->printProgram($value),
				ProgramModule::class => $this->printProgramModule($value),
					ModuleComment::class => $this->printModuleComment($value),
					ModuleExpression::class => $this->printModuleExpression($value),
						ConstantAssignmentTerm::class => $this->printConstantAssignmentTerm($value),
						TypeCastTerm::class => $this->printTypeCastTerm($value),
						TypeDefinitionTerm::class => $this->printTypeDefinitionTerm($value),

			EnumerationValue::class => $this->printEnumerationValue($value),
			FunctionValue::class => $this->printFunctionValue($value),
			ClosureValue::class => $this->printClosureValue($value),
			LiteralValue::class => $this->printLiteralValue($value),
			DictValue::class => $this->printDictValue($value),
			ListValue::class => $this->printListValue($value),
			TypeValue::class => $this->printTypeValue($value),
			SubtypeValue::class => $this->printSubtypeValue($value),

			AnyType::class, NothingType::class,
			BooleanType::class, TrueType::class, NullType::class,
			FalseType::class => $this->printSimpleType($value),

			MutableValue::class => $this->printMutableValue($value),

			ArrayType::class => $this->printArrayType($value),
			EnumerationValueType::class => $this->printEnumerationValueType($value),
			FunctionReturnType::class => $this->printFunctionReturnType($value),
			FunctionType::class => $this->printFunctionType($value),
			IntegerType::class => $this->printIntegerType($value),
			IntersectionType::class => $this->printIntersectionType($value),
			MapType::class => $this->printMapType($value),
			MutableType::class => $this->printMutableType($value),
			RealType::class => $this->printRealType($value),
			RecordType::class => $this->printRecordType($value),
			StringType::class => $this->printStringType($value),
			TupleType::class => $this->printTupleType($value),
			TypeType::class => $this->printTypeType($value),
			UnionType::class => $this->printUnionType($value),

			BinaryTerm::class => $this->printBinaryTerm($value),
			CatchTerm::class => $this->printCatchTerm($value),
			ConstantTerm::class => $this->printConstantTerm($value),
			ConstructorCallTerm::class => $this->printConstructorCallTerm($value),
			DefaultMatchTerm::class => $this->printDefaultMatchTerm($value),
			FunctionBodyExpression::class => $this->printFunctionBodyExpression($value),
			FunctionCallTerm::class => $this->printFunctionCallTerm($value),
			LoopTerm::class => $this->printLoopTerm($value),
			MatchIfTerm::class => $this->printMatchIfTerm($value),
			MatchPairTerm::class => $this->printMatchPairTerm($value),
			MatchTrueTerm::class => $this->printMatchTrueTerm($value),
			MatchTypePairTerm::class => $this->printMatchTypePairTerm($value),
			MatchValueTerm::class => $this->printMatchValueTerm($value),
			MethodCallTerm::class => $this->printMethodCallTerm($value),
			PropertyAccessTerm::class => $this->printPropertyAccessTerm($value),
			RecordTerm::class => $this->printRecordTerm($value),
			ReturnTerm::class => $this->printReturnTerm($value),
			SequenceTerm::class => $this->printSequenceTerm($value),
			ThrowTerm::class => $this->printThrowTerm($value),
			TupleTerm::class => $this->printTupleTerm($value),
			UnaryTerm::class => $this->printUnaryTerm($value),
			VariableAssignmentTerm::class => $this->printVariableAssignmentTerm($value),
			VariableNameTerm::class => $this->printVariableNameTerm($value),
			default => match(true) {
				$value instanceof AliasType => $this->printAliasType($value),
				$value instanceof EnumerationType => $this->printEnumerationType($value),
				$value instanceof SubtypeType => $this->printSubtypeType($value),
				default => "(TODO " . $value::class . ')'
			}
		};
	}

	private function printProgram(Program $program): string {
		$result = [];
		foreach($program->modules as $programModule) {
			$result[] = $this->printNode($programModule);
		}
		return implode($result);
	}

	private function printProgramModule(ProgramModule $programModule): string {
		$result = [];
		foreach($programModule->moduleElements as $moduleElement) {
			$result[] = $this->printNode($moduleElement);
		}
		return '<details open><summary>' . htmlspecialchars($programModule->moduleName) . '</summary>' .
			implode($result) . '</details>';
	}

	private function printModuleComment(ModuleComment $moduleComment): string {
		return '<div class="module-comment">//' . $moduleComment->commentText . '</div>';
	}

	private function printModuleExpression(ModuleExpression $moduleExpression): string {
		return '<div class="module-expression">' . $this->printNode($moduleExpression->expression) . '</div>';
	}

	private function printConstantAssignmentTerm(ConstantAssignmentTerm $constantAssignmentTerm): string {
		return '<span class="constant-assignment-term">' .
			'<span class="variable-name">' . $constantAssignmentTerm->variableName .
			'</span> <span class="variable-assignment-op">=</span> ' .
			'<span class="variable-assignment-op">' . $this->printNode($constantAssignmentTerm->constant) .
		'</span>;</span>';
	}

	private function printVariableNameTerm(VariableNameTerm $variableNameTerm): string {
		return '<span class="variable-name-term">' .
			'<span class="variable-name">' . $variableNameTerm->variableName .
			'</span></span>';
	}

	private function printVariableAssignmentTerm(VariableAssignmentTerm $variableAssignmentTerm): string {
		return '<div class="variable-assignment-term">' .
			'<span class="variable-name">' . $variableAssignmentTerm->variableName .
			'</span> <span class="variable-assignment-op">=</span> ' .
			'<span class="variable-assignment-op">' . $this->printNode($variableAssignmentTerm->value) .
		'</span></div>';
	}

	private function printTypeCastTerm(TypeCastTerm $typeCastTerm): string {
		return '<div class="type-cast-term">' .
			'<span class="type-name">' . $typeCastTerm->castFromType->typeName . '</span>' .
			'<span class="cast-op"> ==&gt; </span>' .
			'<span class="type-name">' . $typeCastTerm->castToType->typeName . '</span>' .
			'<span class="function-definition-op"> :: </span>' .
			'<span class="variable-assignment-op">' . $this->printNode($typeCastTerm->functionBody) .
		'</span>;</div>';
	}

	private function printTypeDefinitionTerm(TypeDefinitionTerm $typeDefinitionTerm): string {
		return '<div class="type-definition-term">' .
			$this->printNode($typeDefinitionTerm->type) .
		'</div>';
	}

	private function printEnumerationType(EnumerationType $enumerationType): string {
		return '<span class="type-definition-name">' . $enumerationType->typeName . '</span>';
	}

	private function printAliasType(AliasType $aliasType): string {
		return '<span class="type-definition-name">' . $aliasType->typeName() . '</span>';
	}

	private function isEmptyExpression(Expression $expression): bool {
		if ($expression instanceof ConstantTerm &&
			$expression->constant instanceof LiteralValue &&
			$expression->constant->literal instanceof NullLiteral
		) {
			return true;
		}
		if ($expression instanceof SequenceTerm && count($expression->terms) === 1) {
			return $this->isEmptyExpression($expression->terms[0]);
		}
		return false;
	}

	private function printSubtypeType(SubtypeType $subtypeType): string {
		return '<span class="type-definition-name">' . $subtypeType->typeName() . '</span>';
	}

	private function printErrorType(ErrorType $errorType): string {
		$pieces = [];
		$hasError = !($errorType->errorType instanceof NothingType);
		if ($errorType->anyError || $hasError) {
			$pieces[] =
				'<span class="error-type error-type-op-' . ($errorType->anyError ? 'any' : 'specific') . '"> ' .
				($errorType->anyError ? '@@' : '@')
				. ' </span>';
			if ($hasError) {
				$pieces[] = '<span class="error-return-type">' .
					$this->printNode($errorType->errorType) . '</span>';
			}
		}
		return implode(' ', $pieces);
	}

	private function printTupleTerm(TupleTerm $tupleTerm): string {
		$values = array_map($this->printNode(...), $tupleTerm->values);
		$str = implode(', ', $values);
		$isLong = strlen(strip_tags($str)) > 40;
		return '<span class="tuple-term ' . ($isLong ? 'multiline-term' : '' ) . '">[' .
			($isLong ? '<br/>' : '') .
			($isLong ? implode(',<br/> ', $values) : $str) .
			($isLong ? '<br/>' : '') .
		']</span>';
	}

	private function printListValue(ListValue $listValue): string {
		$values = array_map($this->printNode(...), $listValue->items);
		$str = implode(', ', $values);
		$isLong = strlen(strip_tags($str)) > 40;
		return '<span class="tuple-constant ' . ($isLong ? 'multiline-term' : '' ) . '">[' .
			($isLong ? '<br/>' : '') .
			($isLong ? implode(',<br/> ', $values) : $str) .
			($isLong ? '<br/>' : '') .
		']</span>';
	}

	private function printRecordTerm(RecordTerm $recordTerm): string {
		$keys = array_map(static fn(string $key): string =>
			'<span class="record-key">' . $key . '</span>',
			array_keys($recordTerm->values));
		$values = array_map($this->printNode(...), $recordTerm->values);
		$result = array_map(static fn(string $key, string $value): string =>
			'<span class="record-key-pair">' . (
				trim(strip_tags($value)) ===
				ucfirst(trim(strip_tags($key))) ?
					'<span class="record-key">~</span>' .
					'<span class="record-key-same-value">' . $value . '</span>' :
					$key . ': ' . $value
			) . '</span>',
			$keys, $values);
		$str = implode(', ', $result);
		$isLong = strlen(strip_tags($str)) > 40;
		return '<span class="record-term ' . ($isLong ? 'multiline-term' : '' ) . '">[' .
			($isLong ? '<br/>' : '') .
			($isLong ? implode(',<br/> ', $result) : $str) .
			($isLong ? '<br/>' : '') .
		']</span>';
	}

	private function printDictValue(DictValue $dictValue): string {
		$keys = array_map(static fn(string $key): string =>
			'<span class="record-key">' . $key . '</span>',
			array_keys($dictValue->items));
		$values = array_map($this->printNode(...), $dictValue->items);
		$result = array_map(static fn(string $key, string $value): string =>
			'<span class="record-key-pair">' . (
				trim(strip_tags($value)) ===
				ucfirst(trim(strip_tags($key))) ?
					'<span class="record-key">~</span>' .
					'<span class="record-key-same-value">' . $value . '</span>' :
					$key . ': ' . $value
			) . '</span>',
			$keys, $values);
		$str = implode(', ', $result);
		$isLong = strlen(strip_tags($str)) > 40;
		return '<span class="record-constant ' . ($isLong ? 'multiline-term' : '' ) . '">[' .
			($isLong ? '<br/>' : '') .
			($isLong ? implode(',<br/> ', $result) : $str) .
			($isLong ? '<br/>' : '') .
		']</span>';
	}

	private function printIntersectionType(IntersectionType $intersectionType): string {
		$values = array_map($this->printNode(...), $intersectionType->types);
		$str = implode('&', $values);
		return '<span class="intersection-type-term">(' . $str . ')</span>';
	}

	private function printUnionType(UnionType $unionType): string {
		$values = array_map($this->printNode(...), $unionType->types);
		$str = implode('|', $values);
		return '<span class="union-type-term">(' . $str . ')</span>';
	}

	private function printTupleType(TupleType $tupleType): string {
		$values = array_map($this->printNode(...), $tupleType->types);
		$str = implode(', ', $values);
		$isLong = strlen(strip_tags($str)) > 40;
		return '<span class="tuple-type-term ' . ($isLong ? 'multiline-term' : '' ) . '">[' .
			($isLong ? '<br/>' : '') .
			($isLong ? implode(',<br/> ', $values) : $str) .
			($isLong ? '<br/>' : '') .
		']</span>';
	}

	private function printRecordType(RecordType $recordType): string {
		$keys = array_map(static fn(string $key): string =>
			'<span class="record-key">' . $key . '</span>',
			array_keys($recordType->types));
		$values = array_map($this->printNode(...), $recordType->types);
		$result = array_map(static fn(string $key, string $value): string =>
			'<span class="record-key-pair">' . (
				trim(strip_tags($value)) ===
				ucfirst(trim(strip_tags($key))) ?
					'<span class="record-key">~</span>' .
					'<span class="record-key-same-value">' . $value . '</span>' :
					$key . ': ' . $value
			) . '</span>',
			$keys, $values);
		$str = implode(', ', $result);
		$isLong = strlen(strip_tags($str)) > 40;
		return '<span class="record-type-term ' . ($isLong ? 'multiline-term' : '' ) . '">[' .
			($isLong ? '<br/>' : '') .
			($isLong ? implode(',<br/> ', $result) : $str) .
			($isLong ? '<br/>' : '') .
		']</span>';
	}

	private function printLiteralValue(LiteralValue $literalValue): string {
		$cls = strtolower(str_ireplace(
			'cast\model\program\literal\\', '', $literalValue->literal::class
		));
		return '<span class="literal-constant literal-constant-' . $cls . '"> ' .
			$literalValue->literal . ' </span>';
	}

	private function printTypeValue(TypeValue $typeValue): string {
		return '<span class="type-constant">' . $this->printNode($typeValue->type) . ' </span>';
	}

	private function printMutableValue(MutableValue $subtypeValue): string {
		return '<span class="mutable-value">' .
			$this->printNode($subtypeValue->targetValue) .
		'</span>';
	}

	private function printSubtypeValue(SubtypeValue $subtypeValue): string {
		$p = $this->printNode($subtypeValue->baseValue);
		$pText = strip_tags($p);
		$hasBrackets = str_starts_with($pText, '[') && str_ends_with($pText, ']');
		return '<span class="subtype-value">' .
			'<span class="type-definition-name">' . $subtypeValue->typeName . '</span>' .
			($hasBrackets ? $p :
				'<span class="constructor-call-op">(</span>' .
				$p .
				'<span class="constructor-call-op">)</span>'
			) .
		' </span>';
	}

	private function printIntegerType(IntegerType $Type): string {
		return '<span class="integer-type-term">' .
			'<span class="type-name">Integer</span>' .
			$this->printRange($Type->range) .
		' </span>';
	}

	private function printRealType(RealType $Type): string {
		return '<span class="real-type-term">' .
			'<span class="type-name">Real</span>' .
			$this->printRange($Type->range) .
		'</span>';
	}

	private function printStringType(StringType $Type): string {
		return '<span class="string-type-term">' .
			'<span class="type-name">String</span>' .
			$this->printLengthRange($Type->range) .
		'</span>';
	}

	private function printArrayType(ArrayType $Type): string {
		return '<span class="array-type-term">' .
			'<span class="type-name">Array</span>' .
			$this->printLengthRange($Type->range, $Type->itemType) .
		'</span>';
	}

	private function printMapType(MapType $Type): string {
		return '<span class="map-type-term">' .
			'<span class="type-name">Map</span>' .
			$this->printLengthRange($Type->range, $Type->itemType) .
		'</span>';
	}

	private function printSimpleType(
		AnyType|NothingType|BooleanType|TrueType|FalseType|NullType $type
	): string {
		$x = substr($type::class, 0, -4);
		$x = substr($x, strrpos($x, '\\', ) + 1);
		return '<span class="simple-type-term">' .
			'<span class="type-name">' . $x . '</span>' .
		'</span>';
	}


	private function printMutableType(MutableType $mutableType): string {
		$hasType = !($mutableType->refType instanceof AnyType ||
			($mutableType->refType instanceof NamedType &&
			$mutableType->refType->typeName()->identifier === 'Any')
		);
		return '<span class="type-type-term">' .
			'<span class="type-name">Mutable</span>' .
			($hasType ? ('<span class="type-parameter">&lt;' .
				$this->printNode($mutableType->refType) . '&gt;</span>') : '') .
		'</span>';
	}

	private function printTypeType(TypeType $typeType): string {
		$hasType = !($typeType->refType instanceof AnyType ||
			($typeType->refType instanceof NamedType &&
			$typeType->refType->typeName()->identifier === 'Any')
		);
		return '<span class="type-type-term">' .
			'<span class="type-name">Type</span>' .
			($hasType ? ('<span class="type-parameter">&lt;' .
				$this->printNode($typeType->refType) . '&gt;</span>') : '') .
		'</span>';
	}

	private function printRange(IntegerRange|RealRange $range): string {
		$min = $range->minValue === MinusInfinity::value ? '' : $range->minValue;
		$max = $range->maxValue === PlusInfinity::value ? '' : $range->maxValue;
		return $min === '' && $max === '' ? '' :
			'<span class="type-range">' .
			'&lt;' .
				'<span class="type-range-min">' . $min . '</span>' . '..' .
				'<span class="type-range-max">' . $max . '</span>' . '&gt;' .
			'</span>';
	}

	private function printLengthRange(LengthRange $range, Type $type = new AnyType): string {
		$min = $range->minLength === 0 ? '' : $range->minLength;
		$max = $range->maxLength === PlusInfinity::value ? '' : $range->maxLength;
		$hasType = !($type instanceof AnyType ||
					($type instanceof NamedType &&
					$type->typeName()->identifier === 'Any'));
		return $min === '' && $max === '' ? (
			$hasType ? ('<span class="type-parameter">&lt;' . $this->printNode($type) . '&gt;</span>') : ''
		) :
			'<span class="type-range">' .
			'&lt;' .
				($hasType ? ('<span class="type-parameter">' . $this->printNode($type) . '</span>, ') : '') .
				'<span class="type-range-min">' . $min . '</span>' . '..' .
				'<span class="type-range-max">' . $max . '</span>' . '&gt;' .
			'</span>';
	}

	private function printEnumerationValue(EnumerationValue $enumerationValue): string {
		return '<span class="enumeration-constant">' .
			'<span class="type-name">' . $enumerationValue->enumTypeName . '</span>.' .
			'<span class="enumeration-value">' . $enumerationValue->enumValue . '</span>' .
		' </span>';
	}

	private function printEnumerationValueType(EnumerationValueType $enumerationValueType): string {
		return '<span class="enumeration-value-type-term">' .
			'<span class="type-name">' . $enumerationValueType->enumType . '</span>' .
			'<span class="enumeration-type-value">[' .
			'<span class="enumeration-value">' . $enumerationValueType->enumValue . '</span>]</span>' .
		' </span>';
	}

	private function printFunctionType(FunctionType $functionType): string {
		return '<span class="function-type-term">' .
			'<span class="function-op"> ^ </span>' .
			$this->printNode($functionType->parameterType) .
			'<span class="function-op-return"> =&gt; </span>' .
			$this->printNode($functionType->returnType) .
		' </span>';
	}

	private function printFunctionReturnType(FunctionReturnType $functionReturnType): string {
		return '<span class="function-return-type-term">' .
			$this->printNode($functionReturnType->returnType) .
			$this->printErrorType($functionReturnType->errorType) .
		' </span>';
	}

	private function printFunctionBodyExpression(FunctionBodyExpression $functionBodyExpression): string {
		return '<span class="function-body-term">' .
			'[function body]' . //$this->printNode($functionBodyExpression->body) .
		' </span>';
	}

	private function printConstantTerm(ConstantTerm $constantTerm): string {
		return '<span class="constant-term">' .
			$this->printNode($constantTerm->constant) .
		' </span>';
	}

	private function printSequenceTerm(SequenceTerm $sequenceTerm): string {
		$terms = array_map(fn(Term $term): string =>
			'<span class="sequence-term-item">' .
				$this->printNode($term) .
				'<span class="sequence-separator">;</span>' .
			'</span>', $sequenceTerm->terms);
		$str = implode($terms);
		$isLong = strlen(trim(strip_tags($str))) > 60;
		return '<span class="sequence-term ' . ($isLong ? 'multiline-term' : '' ) . '">{' .
			($isLong ? '<br/>' : '') .
			($isLong ? implode('<br/>', $terms) : $str) .
			($isLong ? '<br/>' : '') .
		'}</span>';
	}

	private function printPropertyAccessTerm(PropertyAccessTerm $propertyAccessTerm): string {
		return '<span class="property-access-term">' .
			$this->printNode($propertyAccessTerm->target) .
			'<span class="property-op">.</span>' .
			'<span class="property-access-property">' .
				htmlspecialchars($propertyAccessTerm->propertyName) . '</span>' .
		'</span>';
	}

	private function printFunctionCallTerm(FunctionCallTerm $functionCallTerm): string {
		$hasBrackets =
			!$functionCallTerm->parameter instanceof TupleTerm &&
			!$functionCallTerm->parameter instanceof RecordTerm;
		$skipParameter = $this->isEmptyExpression($functionCallTerm->parameter);
		return '<span class="function-call-term">' .
			$this->printNode($functionCallTerm->target) .
			'<span class="function-call-op">' . ($hasBrackets ? '(' : '') .
				($skipParameter ? '' :
				'<span class="function-call-parameter">' .
					$this->printNode($functionCallTerm->parameter) .
				'</span>') .
			($hasBrackets ? ')' : '') . '</span>' .
		'</span>';
	}

	private function printMethodCallTerm(MethodCallTerm $methodCallTerm): string {
		$hasBrackets =
			!$methodCallTerm->parameter instanceof TupleTerm &&
			!$methodCallTerm->parameter instanceof RecordTerm;
		$skipParameter = $this->isEmptyExpression($methodCallTerm->parameter);
		return '<span class="method-call-term">' .
			$this->printNode($methodCallTerm->target) .
			'<span class="method-op">-&gt;</span>' .
			'<span class="method-call-method">' .
				htmlspecialchars($methodCallTerm->methodName) . '</span>' .
			($skipParameter ? '' :
			'<span class="method-call-op">' . ($hasBrackets ? '(' : '') .
				'<span class="method-call-parameter">' .
					$this->printNode($methodCallTerm->parameter) .
				'</span>' .
			($hasBrackets ? ')' : '') . '</span>') .
		'</span>';
	}

	private function printConstructorCallTerm(ConstructorCallTerm $constructorCallTerm): string {
		$hasBrackets =
			!$constructorCallTerm->parameter instanceof TupleTerm &&
			!$constructorCallTerm->parameter instanceof RecordTerm;
		$skipParameter = $this->isEmptyExpression($constructorCallTerm->parameter);
		return '<span class="constructor-call-term">' .
			$this->printNode($constructorCallTerm->type) .
			'<span class="constructor-call-op">' . ($hasBrackets ? '(' : '') .
				($skipParameter ? '' :
				'<span class="constructor-call-parameter">' .
					$this->printNode($constructorCallTerm->parameter) .
				'</span>') .
			($hasBrackets ? ')' : '') . '</span>' .
		'</span>';
	}

	private function printMatchValueTerm(MatchValueTerm $matchValueTerm): string {
		$cases = [];
		foreach($matchValueTerm->parameters as $parameter) {
			$cases[] = $this->printNode($parameter);
		}
		return '<span class="match-value-term">' .
			'<span class="match-target">(' .
				$this->printNode($matchValueTerm->target) .
			')</span>' .
			'<span class="match-value-op"> ?= </span>' .
			'<span class="match-cases"> {<br/>' .
				implode($cases) .
			'}</span>' .
		'</span>';
	}

	private function printMatchTrueTerm(MatchTrueTerm $matchTrueTerm): string {
		$cases = [];
		foreach($matchTrueTerm->parameters as $parameter) {
			$cases[] = $this->printNode($parameter);
		}
		return '<span class="match-true-term">' .
			'<span class="match-true-op"> ?? </span>' .
			'<span class="match-cases"> {<br/>' .
				implode($cases) .
			'}</span>' .
		'</span>';
	}

	private function printMatchIfTerm(MatchIfTerm $matchValueTerm): string {
		$true = $this->printNode($matchValueTerm->trueExpression);
		$false = $this->printNode($matchValueTerm->falseExpression);
		$br = strlen(trim(strip_tags($true))) +
			strlen(trim(strip_tags($false))) > 50 ? '<br/>' : '';
		return '<span class="match-value-term">' .
			'<span class="match-target">(' .
				$this->printNode($matchValueTerm->target) .
			')</span>' .
			'<span class="match-value-op"> ?== </span>' .
			'<span class="match-cases"> {' . $br .
				'<span class="match-case">' .
					'<span class="match-case-op">|</span> ' .
					$true .
				'</span>' . $br .
				'<span class="match-case">' .
					'<span class="match-case-op">|</span> ' .
					$false .
				'</span>' . $br .
			'}</span>' .
		'</span>';
	}

	private function printMatchTypePairTerm(MatchTypePairTerm $matchTypePairTerm): string {
		return '<div class="match-type-pair-term">' .
			'<span class="match-type-pair-op">| </span>' .
			$this->printNode($matchTypePairTerm->matchedExpression) .
			'<span class="match-type-pair-op">: </span>' .
			$this->printNode($matchTypePairTerm->valueExpression) .
		'</div>';
	}

	private function printMatchPairTerm(MatchPairTerm $matchPairTerm): string {
		return '<div class="match-pair-term">' .
			'<span class="match-pair-op">| </span>' .
			$this->printNode($matchPairTerm->matchedExpression) .
			'<span class="match-pair-op">: </span>' .
			$this->printNode($matchPairTerm->valueExpression) .
		'</div>';
	}

	private function printDefaultMatchTerm(DefaultMatchTerm $defaultMatchTerm): string {
		return '<div class="default-match-term">' .
			'<span class="default-match-op">| <span class="default-match-sign">~</span>: </span>' .
			$this->printNode($defaultMatchTerm->valueExpression) .
		'</div>';
	}

	private function printLoopTerm(LoopTerm $loopTerm): string {
		return '<span class="loop-term">' .
			'<span class="loop-check-expression">(' .
				$this->printNode($loopTerm->checkExpression) .
			')</span>' .
			'<span class="loop-op"> ' . $loopTerm->loopExpressionType->sign() . ' </span>' .
			'<span class="loop-loop-expression">(' .
				$this->printNode($loopTerm->loopExpression) .
			')</span>' .
		'</span>';
	}

	private function printReturnTerm(ReturnTerm $returnTerm): string {
		return '<span class="return-term">' .
			'<span class="return-op">=&gt;</span>' .
			$this->printNode($returnTerm->returnedValue) .
		'</span>';
	}

	private function printThrowTerm(ThrowTerm $throwTerm): string {
		return '<span class="throw-term">' .
			'<span class="throw-op"> @</span>' .
			$this->printNode($throwTerm->thrownValue) .
		'</span>';
	}

	private function printCatchTerm(CatchTerm $catchTerm): string {
		return '<span class="catch-term">' .
			'<span class="error-type error-type-op-' . ($catchTerm->anyType ? 'any' : 'specific') . '"> ' .
			($catchTerm->anyType ? '@@' : '@') . ' </span>' .
			$this->printNode($catchTerm->catchTarget) .
		'</span>';
	}

	private function printUnaryTerm(UnaryTerm $unaryTerm): string {
		return '<span class="unary-term">' .
			'<span class="unary-op">' . $unaryTerm->operation->value . ' </span> ' .
			$this->printNode($unaryTerm->expression) .
		'</span>';
	}

	private function printBinaryTerm(BinaryTerm $binaryTerm): string {
		return '<span class="binary-term">' .
			$this->printNode($binaryTerm->firstExpression) .
			' <span class="binary-op">' . htmlspecialchars($binaryTerm->operation->value) . ' </span> ' .
			$this->printNode($binaryTerm->secondExpression) .
		'</span>';
	}

	private function printFunctionValue(FunctionValue $functionValue): string {
		return '<span class="function-constant">' .
			'<span class="function-op"> ^ </span>' .
			$this->printNode($functionValue->parameterType) .
			'<span class="function-op-return"> =&gt; </span>' .
			$this->printNode($functionValue->returnType) .
			'<span class="function-definition-op"> :: </span>' .
			$this->printNode($functionValue->functionBody) .
		' </span>';
	}

	private function printClosureValue(ClosureValue $closureValue): string {
		return '<span class="function-constant">' .
			'<span class="function-op"> [closure]^ </span>' .
			$this->printNode($closureValue->function->parameterType) .
			'<span class="function-op-return"> =&gt; </span>' .
			$this->printNode($closureValue->function->returnType) .
			'<span class="function-definition-op"> :: </span>' .
			$this->printNode($closureValue->function->functionBody) .
		' </span>';
	}

}