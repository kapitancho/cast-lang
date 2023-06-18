<?php /** @noinspection NestedTernaryOperatorInspection */

namespace Cast\Service\Program;

use Cast\Model\Program\Constant\EnumerationConstant;
use Cast\Model\Program\Constant\FunctionConstant;
use Cast\Model\Program\Constant\LiteralConstant;
use Cast\Model\Program\Constant\RecordConstant;
use Cast\Model\Program\Constant\TupleConstant;
use Cast\Model\Program\Constant\TypeConstant;
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
use Cast\Model\Program\Term\MatchTypeTerm;
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
use Cast\Model\Program\TypeDefinition\AliasTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\EnumerationTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\SubtypeDefinitionTerm;

final class CodeHtmlPrinter {
	public function print(Program $program): string {
		return $this->printNode($program);
	}

	private function printNode(Node $node): string {
		return match($node::class) {
			Program::class => $this->printProgram($node),
				ProgramModule::class => $this->printProgramModule($node),
					ModuleComment::class => $this->printModuleComment($node),
					ModuleExpression::class => $this->printModuleExpression($node),
						ConstantAssignmentTerm::class => $this->printConstantAssignmentTerm($node),
						TypeCastTerm::class => $this->printTypeCastTerm($node),
						TypeDefinitionTerm::class => $this->printTypeDefinitionTerm($node),
							AliasTypeDefinitionTerm::class => $this->printAliasTypeDefinitionTerm($node),
							EnumerationTypeDefinitionTerm::class => $this->printEnumerationTypeDefinitionTerm($node),
							SubtypeDefinitionTerm::class => $this->printSubtypeDefinitionTerm($node),

			EnumerationConstant::class => $this->printEnumerationConstant($node),
			FunctionConstant::class => $this->printFunctionConstant($node),
			LiteralConstant::class => $this->printLiteralConstant($node),
			RecordConstant::class => $this->printRecordConstant($node),
			TupleConstant::class => $this->printTupleConstant($node),
			TypeConstant::class => $this->printTypeConstant($node),

			AnyTypeTerm::class, NothingTypeTerm::class,
			BooleanTypeTerm::class, TrueTypeTerm::class, NullTypeTerm::class,
			FalseTypeTerm::class => $this->printSimpleTypeTerm($node),

			ArrayTypeTerm::class => $this->printArrayTypeTerm($node),
			EnumerationValueTypeTerm::class => $this->printEnumerationValueTypeTerm($node),
			FunctionReturnTypeTerm::class => $this->printFunctionReturnTypeTerm($node),
			FunctionTypeTerm::class => $this->printFunctionTypeTerm($node),
			IntegerTypeTerm::class => $this->printIntegerTypeTerm($node),
			IntersectionTypeTerm::class => $this->printIntersectionTypeTerm($node),
			MapTypeTerm::class => $this->printMapTypeTerm($node),
			MutableTypeTerm::class => $this->printMutableTypeTerm($node),
			RealTypeTerm::class => $this->printRealTypeTerm($node),
			RecordTypeTerm::class => $this->printRecordTypeTerm($node),
			StringTypeTerm::class => $this->printStringTypeTerm($node),
			TupleTypeTerm::class => $this->printTupleTypeTerm($node),
			TypeNameTerm::class => $this->printTypeNameTerm($node),
			TypeTypeTerm::class => $this->printTypeTypeTerm($node),
			UnionTypeTerm::class => $this->printUnionTypeTerm($node),

			BinaryTerm::class => $this->printBinaryTerm($node),
			CatchTerm::class => $this->printCatchTerm($node),
			ConstantTerm::class => $this->printConstantTerm($node),
			ConstructorCallTerm::class => $this->printConstructorCallTerm($node),
			DefaultMatchTerm::class => $this->printDefaultMatchTerm($node),
			FunctionBodyTerm::class => $this->printFunctionBodyTerm($node),
			FunctionCallTerm::class => $this->printFunctionCallTerm($node),
			LoopTerm::class => $this->printLoopTerm($node),
			MatchIfTerm::class => $this->printMatchIfTerm($node),
			MatchPairTerm::class => $this->printMatchPairTerm($node),
			MatchTrueTerm::class => $this->printMatchTrueTerm($node),
			MatchTypePairTerm::class => $this->printMatchTypePairTerm($node),
			MatchTypeTerm::class => $this->printMatchTypeTerm($node),
			MatchValueTerm::class => $this->printMatchValueTerm($node),
			MethodCallTerm::class => $this->printMethodCallTerm($node),
			PropertyAccessTerm::class => $this->printPropertyAccessTerm($node),
			RecordTerm::class => $this->printRecordTerm($node),
			ReturnTerm::class => $this->printReturnTerm($node),
			SequenceTerm::class => $this->printSequenceTerm($node),
			ThrowTerm::class => $this->printThrowTerm($node),
			TupleTerm::class => $this->printTupleTerm($node),
			UnaryTerm::class => $this->printUnaryTerm($node),
			VariableAssignmentTerm::class => $this->printVariableAssignmentTerm($node),
			VariableNameTerm::class => $this->printVariableNameTerm($node),
			default => "(TODO " . $node::class . ')'
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
		return '<span class="variable-assignment-term">' .
			'<span class="variable-name">' . $variableAssignmentTerm->variableName .
			'</span> <span class="variable-assignment-op">=</span> ' .
			'<span class="variable-assignment-op">' . $this->printNode($variableAssignmentTerm->value) .
		'</span></span>';
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

	private function printEnumerationTypeDefinitionTerm(EnumerationTypeDefinitionTerm $enumerationTypeDefinitionTerm): string {
		return '<div class="enumeration-type-definition-term">' .
			'<span class="type-definition-name">' . $enumerationTypeDefinitionTerm->typeName . '</span>' .
			'<span class="variable-assignment-op"> = </span>' .
			'<span class="enumeration-values">[<span class="enumeration-value">' .
				implode('</span>, <span class="enumeration-value">', $enumerationTypeDefinitionTerm->values) .
			'</span>]</span>' .
		';</div>';
	}

	private function printAliasTypeDefinitionTerm(AliasTypeDefinitionTerm $aliasTypeDefinitionTerm): string {
		return '<div class="enumeration-type-definition-term">' .
			'<span class="type-definition-name">' . $aliasTypeDefinitionTerm->typeName . '</span>' .
			'<span class="variable-assignment-op"> = </span>' .
			$this->printNode($aliasTypeDefinitionTerm->aliasedType) .
		';</div>';
	}

	private function isEmptyTerm(Term $term): bool {
		if ($term instanceof ConstantTerm &&
			$term->constant instanceof LiteralConstant &&
			$term->constant->literal instanceof NullLiteral
		) {
			return true;
		}
		if ($term instanceof SequenceTerm && count($term->terms) === 1) {
			return $this->isEmptyTerm($term->terms[0]);
		}
		return false;
	}

	private function printSubtypeDefinitionTerm(SubtypeDefinitionTerm $subtypeDefinitionTerm): string {
		$body = $this->isEmptyTerm($subtypeDefinitionTerm->functionBody->body) ? '' :
			'<span class="function-definition-op"> :: </span>' .
			'<span class="variable-assignment-op">' .
				$this->printNode($subtypeDefinitionTerm->functionBody) .
			'</span>';
		return '<div class="enumeration-type-definition-term">' .
			'<span class="type-definition-name">' . $subtypeDefinitionTerm->typeName . '</span>' .
			'<span class="variable-assignment-op"> &lt;: </span>' .
			$this->printNode($subtypeDefinitionTerm->baseType) .
			$this->printErrorTypeTerm($subtypeDefinitionTerm->errorType) .
			$body .
		';</div>';
	}

	private function printErrorTypeTerm(ErrorTypeTerm $errorTypeTerm): string {
		$pieces = [];
		$hasError = !($errorTypeTerm->errorType instanceof NothingTypeTerm);
		if ($errorTypeTerm->anyError || $hasError) {
			$pieces[] =
				'<span class="error-type error-type-op-' . ($errorTypeTerm->anyError ? 'any' : 'specific') . '"> ' .
				($errorTypeTerm->anyError ? '@@' : '@')
				. ' </span>';
			if ($hasError) {
				$pieces[] = '<span class="error-return-type">' .
					$this->printNode($errorTypeTerm->errorType) . '</span>';
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

	private function printTupleConstant(TupleConstant $tupleConstant): string {
		$values = array_map($this->printNode(...), $tupleConstant->items);
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

	private function printRecordConstant(RecordConstant $recordConstant): string {
		$keys = array_map(static fn(string $key): string =>
			'<span class="record-key">' . $key . '</span>',
			array_keys($recordConstant->items));
		$values = array_map($this->printNode(...), $recordConstant->items);
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

	private function printIntersectionTypeTerm(IntersectionTypeTerm $intersectionTypeTerm): string {
		$values = array_map($this->printNode(...), $intersectionTypeTerm->types);
		$str = implode('&', $values);
		return '<span class="intersection-type-term">(' . $str . ')</span>';
	}

	private function printUnionTypeTerm(UnionTypeTerm $unionTypeTerm): string {
		$values = array_map($this->printNode(...), $unionTypeTerm->types);
		$str = implode('|', $values);
		return '<span class="union-type-term">(' . $str . ')</span>';
	}

	private function printTupleTypeTerm(TupleTypeTerm $tupleTypeTerm): string {
		$values = array_map($this->printNode(...), $tupleTypeTerm->types);
		$str = implode(', ', $values);
		$isLong = strlen(strip_tags($str)) > 40;
		return '<span class="tuple-type-term ' . ($isLong ? 'multiline-term' : '' ) . '">[' .
			($isLong ? '<br/>' : '') .
			($isLong ? implode(',<br/> ', $values) : $str) .
			($isLong ? '<br/>' : '') .
		']</span>';
	}

	private function printRecordTypeTerm(RecordTypeTerm $recordTypeTerm): string {
		$keys = array_map(static fn(string $key): string =>
			'<span class="record-key">' . $key . '</span>',
			array_keys($recordTypeTerm->types));
		$values = array_map($this->printNode(...), $recordTypeTerm->types);
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

	private function printLiteralConstant(LiteralConstant $literalConstant): string {
		$cls = strtolower(str_ireplace(
			'cast\model\program\literal\\', '', $literalConstant->literal::class
		));
		return '<span class="literal-constant literal-constant-' . $cls . '"> ' .
			$literalConstant->literal . ' </span>';
	}

	private function printTypeConstant(TypeConstant $typeConstant): string {
		return '<span class="type-constant">' . $this->printNode($typeConstant->type) . ' </span>';
	}

	private function printIntegerTypeTerm(IntegerTypeTerm $typeTerm): string {
		return '<span class="integer-type-term">' .
			'<span class="type-name">Integer</span>' .
			$this->printRange($typeTerm->range) .
		' </span>';
	}

	private function printRealTypeTerm(RealTypeTerm $typeTerm): string {
		return '<span class="real-type-term">' .
			'<span class="type-name">Real</span>' .
			$this->printRange($typeTerm->range) .
		'</span>';
	}

	private function printStringTypeTerm(StringTypeTerm $typeTerm): string {
		return '<span class="string-type-term">' .
			'<span class="type-name">String</span>' .
			$this->printLengthRange($typeTerm->range) .
		'</span>';
	}

	private function printArrayTypeTerm(ArrayTypeTerm $typeTerm): string {
		return '<span class="array-type-term">' .
			'<span class="type-name">Array</span>' .
			$this->printLengthRange($typeTerm->range, $typeTerm->itemType) .
		'</span>';
	}

	private function printMapTypeTerm(MapTypeTerm $typeTerm): string {
		return '<span class="map-type-term">' .
			'<span class="type-name">Map</span>' .
			$this->printLengthRange($typeTerm->range, $typeTerm->itemType) .
		'</span>';
	}

	private function printSimpleTypeTerm(
		AnyTypeTerm|NullTypeTerm|NothingTypeTerm|BooleanTypeTerm|TrueTypeTerm|FalseTypeTerm $typeTerm
	): string {
		$x = substr($typeTerm::class, 0, -8);
		$x = substr($x, strrpos($x, '\\', ) + 1);
		return '<span class="simple-type-term">' .
			'<span class="type-name">' . $x . '</span>' .
		'</span>';
	}

	private function printTypeNameTerm(
		TypeNameTerm $typeNameTerm
	): string {
		return '<span class="type-name-term">' .
			'<span class="type-name">' . $typeNameTerm->typeName . '</span>' .
		'</span>';
	}

	private function printMutableTypeTerm(MutableTypeTerm $mutableTypeTerm): string {
		$hasType = !($mutableTypeTerm->refType instanceof AnyTypeTerm ||
			($mutableTypeTerm->refType instanceof TypeNameTerm &&
			$mutableTypeTerm->refType->typeName->identifier === 'Any')
		);
		return '<span class="type-type-term">' .
			'<span class="type-name">Mutable</span>' .
			($hasType ? ('<span class="type-parameter">&lt;' .
				$this->printNode($mutableTypeTerm->refType) . '&gt;</span>') : '') .
		'</span>';
	}

	private function printTypeTypeTerm(TypeTypeTerm $typeTypeTerm): string {
		$hasType = !($typeTypeTerm->refType instanceof AnyTypeTerm ||
			($typeTypeTerm->refType instanceof TypeNameTerm &&
			$typeTypeTerm->refType->typeName->identifier === 'Any')
		);
		return '<span class="type-type-term">' .
			'<span class="type-name">Type</span>' .
			($hasType ? ('<span class="type-parameter">&lt;' .
				$this->printNode($typeTypeTerm->refType) . '&gt;</span>') : '') .
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

	private function printLengthRange(LengthRange $range, TypeTerm $typeTerm = new AnyTypeTerm): string {
		$min = $range->minLength === 0 ? '' : $range->minLength;
		$max = $range->maxLength === PlusInfinity::value ? '' : $range->maxLength;
		$hasType = !($typeTerm instanceof AnyTypeTerm ||
					($typeTerm instanceof TypeNameTerm &&
					$typeTerm->typeName->identifier === 'Any'));
		return $min === '' && $max === '' ? (
			$hasType ? ('<span class="type-parameter">&lt;' . $this->printNode($typeTerm) . '&gt;</span>') : ''
		) :
			'<span class="type-range">' .
			'&lt;' .
				($hasType ? ('<span class="type-parameter">' . $this->printNode($typeTerm) . '</span>, ') : '') .
				'<span class="type-range-min">' . $min . '</span>' . '..' .
				'<span class="type-range-max">' . $max . '</span>' . '&gt;' .
			'</span>';
	}

	private function printEnumerationConstant(EnumerationConstant $enumerationConstant): string {
		return '<span class="enumeration-constant">' .
			'<span class="type-name">' . $enumerationConstant->enumTypeName . '</span>.' .
			'<span class="enumeration-value">' . $enumerationConstant->enumValue . '</span>' .
		' </span>';
	}

	private function printEnumerationValueTypeTerm(EnumerationValueTypeTerm $enumerationValueTypeTerm): string {
		return '<span class="enumeration-value-type-term">' .
			'<span class="type-name">' . $enumerationValueTypeTerm->enumTypeName . '</span>' .
			'<span class="enumeration-type-value">[' .
			'<span class="enumeration-value">' . $enumerationValueTypeTerm->enumValue . '</span>]</span>' .
		' </span>';
	}

	private function printFunctionTypeTerm(FunctionTypeTerm $functionTypeTerm): string {
		return '<span class="function-type-term">' .
			'<span class="function-op"> ^ </span>' .
			$this->printNode($functionTypeTerm->parameterType) .
			'<span class="function-op-return"> =&gt; </span>' .
			$this->printNode($functionTypeTerm->returnType) .
		' </span>';
	}

	private function printFunctionReturnTypeTerm(FunctionReturnTypeTerm $functionReturnTypeTerm): string {
		return '<span class="function-return-type-term">' .
			$this->printNode($functionReturnTypeTerm->returnType) .
			$this->printErrorTypeTerm($functionReturnTypeTerm->errorType) .
		' </span>';
	}

	private function printFunctionBodyTerm(FunctionBodyTerm $functionBodyTerm): string {
		return '<span class="function-body-term">' .
			$this->printNode($functionBodyTerm->body) .
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
		$skipParameter = $this->isEmptyTerm($functionCallTerm->parameter);
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
		$skipParameter = $this->isEmptyTerm($methodCallTerm->parameter);
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
		$skipParameter = $this->isEmptyTerm($constructorCallTerm->parameter);
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

	private function printMatchTypeTerm(MatchTypeTerm $matchTypeTerm): string {
		$cases = [];
		foreach($matchTypeTerm->parameters as $parameter) {
			$cases[] = $this->printNode($parameter);
		}
		return '<span class="match-type-term">' .
			'<span class="match-target">(' .
				$this->printNode($matchTypeTerm->target) .
			')</span>' .
			'<span class="match-type-op">?&lt;:</span>' .
			'<span class="match-cases"> {<br/>' .
				implode($cases) .
			'}</span>' .
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

	private function printFunctionConstant(FunctionConstant $functionConstant): string {
		return '<span class="function-constant">' .
			'<span class="function-op"> ^ </span>' .
			$this->printNode($functionConstant->parameterType) .
			'<span class="function-op-return"> =&gt; </span>' .
			$this->printNode($functionConstant->returnType) .
			'<span class="function-definition-op"> :: </span>' .
			$this->printNode($functionConstant->functionBody) .
		' </span>';
	}

}