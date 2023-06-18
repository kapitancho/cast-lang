<?php
/** @noinspection PhpArgumentWithoutNamedIdentifierInspection */
/** @noinspection PhpParamsInspection */

namespace Cast\Service\Program\Builder;

use Cast\Model\Program\{Constant\Constant,
	Constant\EnumerationConstant,
	Constant\FunctionConstant,
	Constant\LiteralConstant,
	Constant\RecordConstant,
	Constant\TupleConstant,
	Constant\TypeConstant,
	Identifier\EnumValueIdentifier,
	Identifier\ModuleNameIdentifier,
	Identifier\PropertyNameIdentifier,
	Identifier\TypeNameIdentifier,
	Identifier\VariableNameIdentifier,
	Literal\BooleanLiteral,
	Literal\IntegerLiteral,
	Literal\NullLiteral,
	Literal\RealLiteral,
	Literal\StringLiteral,
	ModuleComment,
	ModuleExpression,
	Node,
	Program,
	ProgramModule,
	Range\IntegerRange,
	Range\LengthRange,
	Range\MinusInfinity,
	Range\PlusInfinity,
	Range\Range,
	Range\RealRange,
	Term\BinaryOperation,
	Term\BinaryTerm,
	Term\CatchTerm,
	Term\ConstantTerm,
	Term\ConstructorCallTerm,
	Term\DefaultMatchTerm,
	Term\Expression\ConstantAssignmentTerm,
	Term\Expression\TypeCastTerm,
	Term\Expression\TypeDefinitionTerm,
	Term\FunctionBodyTerm,
	Term\FunctionCallTerm,
	Term\LoopExpressionType,
	Term\LoopTerm,
	Term\MatchIfTerm,
	Term\MatchPairTerm,
	Term\MatchTrueTerm,
	Term\MatchTypePairTerm,
	Term\MatchTypeTerm,
	Term\MatchValueTerm,
	Term\MethodCallTerm,
	Term\PropertyAccessTerm,
	Term\RecordTerm,
	Term\ReturnTerm,
	Term\SequenceTerm,
	Term\Term,
	Term\ThrowTerm,
	Term\TupleTerm,
	Term\UnaryOperation,
	Term\UnaryTerm,
	Term\VariableAssignmentTerm,
	Term\VariableNameTerm,
	Type\AnyTypeTerm,
	Type\ArrayTypeTerm,
	Type\BooleanTypeTerm,
	Type\EnumerationValueTypeTerm,
	Type\ErrorTypeTerm,
	Type\FalseTypeTerm,
	Type\FunctionReturnTypeTerm,
	Type\FunctionTypeTerm,
	Type\IntegerTypeTerm,
	Type\IntersectionTypeTerm,
	Type\MapTypeTerm,
	Type\MutableTypeTerm,
	Type\NothingTypeTerm,
	Type\NullTypeTerm,
	Type\RealTypeTerm,
	Type\RecordTypeTerm,
	Type\StringTypeTerm,
	Type\TrueTypeTerm,
	Type\TupleTypeTerm,
	Type\TypeNameTerm,
	Type\TypeTerm,
	Type\TypeTypeTerm,
	Type\UnionTypeTerm,
	TypeDefinition\AliasTypeDefinitionTerm,
	TypeDefinition\EnumerationTypeDefinitionTerm,
	TypeDefinition\SubtypeDefinitionTerm};

final readonly class FromArraySourceBuilder {

	public function build(array $arr): Node {
		return $this->parseNode($arr);
	}

	private function parseNode(array $arr): Node {
		return match($arr['node']) {
			'comment' => $this->parseComment($arr),
			'literal' => $this->parseLiteral($arr),
			'constant' => $this->parseConstant($arr),
			'function_return_type_term' => $this->parseFunctionReturnTypeTerm($arr),
			'module' => $this->parseModule($arr),
			'module_expression' => $this->parseModuleExpression($arr),
			'program' => $this->parseProgram($arr),
			'range' => $this->parseRange($arr),
			'term' => $this->parseTerm($arr),
			'type_term' => $this->parseTypeTerm($arr),
			'type_definition_term' => $this->parseTypeDefinitionTerm($arr),
			default => throw new BuilderException(
				sprintf("Unknown node type: %s", $arr['node'])
			)
		};
	}

	private function getNullAsArray(): array {
		return [
			'node' => 'term',
			'term' => 'constant',
			'constant' => [
				'node' => 'constant',
				'type' => 'literal',
				'literal' => [
					'node' => 'literal',
					'type' => 'null'
				]
			]
		];
	}

	private function parseProgram(array $arr): Program {
		return new Program(... array_map(
			$this->parseNode(...), $arr['modules']
		));
	}

	private function parseModule(array $arr): ProgramModule {
		return new ProgramModule(
			new ModuleNameIdentifier($arr['module_name']),
			... array_map($this->parseNode(...), $arr['elements'])
		);
	}

	private function parseComment(array $arr): Node {
		return new ModuleComment($arr['comment'] ?? '');
	}

	private function parseModuleExpression(array $arr): ModuleExpression {
		return new ModuleExpression(
			match($arr['expression_type']) {
				'type_definition_term' => new TypeDefinitionTerm(
					$this->parseNode($arr['type_definition'])
				),
				'constant_assignment' => new ConstantAssignmentTerm(
					new VariableNameIdentifier($arr['variable_name']),
					$this->parseNode($arr['value'])
				),
				'type_cast' => new TypeCastTerm(
					$this->parseNode($arr['cast']['from_type']),
					$this->parseNode($arr['cast']['to_type']),
					$this->parseNode($arr['cast']['function_body']),
					new ErrorTypeTerm(
						$arr['cast']['any_error'],
						$this->parseNode($arr['cast']['error_type'])
					)
				),
			}
		);
	}

	private function parseTypeDefinitionTerm(array $arr): AliasTypeDefinitionTerm|SubtypeDefinitionTerm|EnumerationTypeDefinitionTerm {
		return match($arr['type']) {
			'alias' => new AliasTypeDefinitionTerm(
				new TypeNameIdentifier($arr['type_name']),
				$this->parseNode($arr['aliased_type']),
			),
			'subtype' => new SubtypeDefinitionTerm(
				new TypeNameIdentifier($arr['type_name']),
				$this->parseNode($arr['base_type']),
				$this->parseNode($arr['function_body']),
				new ErrorTypeTerm(
					$arr['any_error'],
					$this->parseNode($arr['error_type'])
				)
			),
			'enumeration' => new EnumerationTypeDefinitionTerm(
				new TypeNameIdentifier($arr['type_name']),
				... array_map(static fn(string $value): EnumValueIdentifier
					=> new EnumValueIdentifier($value), $arr['values'])
			),
		};
	}

	private function parseTerm(array $arr): Term {
		return match($arr['term']) {
			'binary' => new BinaryTerm(
				BinaryOperation::tryFrom($arr['operation']),
				$this->parseNode($arr['first_target']),
				$this->parseNode($arr['second_target']),
			),
			'catch' => new CatchTerm($this->parseNode($arr['target']), $arr['any_type']),
			'constant' => new ConstantTerm($this->parseNode($arr['constant'])),
			'constructor_call' => new ConstructorCallTerm(
				$this->parseNode($arr['target_type']),
				$this->parseNode($arr['parameter'] ?? $this->getNullAsArray())
			),
			'empty' => new SequenceTerm,
			'function_body' => new FunctionBodyTerm(
				$this->parseNode($arr['body'])
			),
			'function_call' => new FunctionCallTerm(
				$this->parseNode($arr['target']),
				$this->parseNode($arr['parameter'] ?? $this->getNullAsArray())
			),
			'loop' => new LoopTerm(
				$this->parseNode($arr['check_expression']),
				LoopExpressionType::tryFrom($arr['loop_expression_type']),
				$this->parseNode($arr['loop_expression']),
			),
			'match_if' => new MatchIfTerm(
				$this->parseNode($arr['target']),
				$this->parseNode($arr['parameters'][0]),
				$this->parseNode($arr['parameters'][1]),
			),
			'match_value' => new MatchValueTerm(
				$this->parseNode($arr['target']),
				... array_map($this->parseNode(...), $arr['parameters'])
			),
			'match_type' => new MatchTypeTerm(
				$this->parseNode($arr['target']),
				... array_map($this->parseNode(...), $arr['parameters'])
			),
			'match_true' => new MatchTrueTerm(
				... array_map($this->parseNode(...), $arr['parameters'])
			),
			'match_type_pair' => new MatchTypePairTerm(
				$this->parseTypeTerm($arr['matched_expression']),
				$this->parseNode($arr['value_expression']),
			),
			'match_pair' => new MatchPairTerm(
				$this->parseNode($arr['matched_expression']),
				$this->parseNode($arr['value_expression']),
			),
			'default_match_pair' => new DefaultMatchTerm(
				$this->parseNode($arr['value_expression']),
			),
			'method_call' => new MethodCallTerm(
				$this->parseNode($arr['target']),
				new PropertyNameIdentifier($arr['method_name']),
				$this->parseNode($arr['parameter'] ?? $this->getNullAsArray())
			),
			'property_access' => new PropertyAccessTerm(
				$this->parseNode($arr['target']),
				new PropertyNameIdentifier($arr['property_name']),
			),
			'record' => new RecordTerm(... array_map($this->parseNode(...), $arr['value'])),
			'return' => new ReturnTerm($this->parseNode($arr['returned_value'])),
			'sequence' => new SequenceTerm(
				... array_map($this->parseNode(...), $arr['sequence'])
			),
			'throw' => new ThrowTerm($this->parseNode($arr['thrown_value'])),
			'tuple' => new TupleTerm(... array_map($this->parseNode(...), $arr['value'])),
			'unary' => new UnaryTerm(
				UnaryOperation::tryFrom($arr['operation']),
				$this->parseNode($arr['target']),
			),
			'variable_assignment' => new VariableAssignmentTerm(
				new VariableNameIdentifier($arr['variable_name']),
				$this->parseNode($arr['value'])
			),
			'variable_name' => new VariableNameTerm(
				new VariableNameIdentifier($arr['variable_name'] ?? $arr['variable']['variable_name']),
			),
		};
	}

	private function parseFunctionReturnTypeTerm(array $arr): FunctionReturnTypeTerm {
		return new FunctionReturnTypeTerm(
			$this->parseNode($arr['return_type']),
			new ErrorTypeTerm(
				(bool)$arr['any_error'],
				$this->parseNode($arr['error_type']),
			)
		);
	}

	private function parseTypeTerm(array $arr): TypeTerm {
		return match($arr['type']) {
			'any' => new AnyTypeTerm,
			'array' => new ArrayTypeTerm(
				$this->parseNode($arr['item_type']),
				$this->parseNode($arr['length_range'])
			),
			'boolean' => new BooleanTypeTerm,
			'null' => new NullTypeTerm,
			'enumeration_value_type' => new EnumerationValueTypeTerm(
				new TypeNameIdentifier($arr['enumeration']),
				new EnumValueIdentifier($arr['value']),
			),
			'false' => new FalseTypeTerm,
			'function' => new FunctionTypeTerm(
				$this->parseNode($arr['parameter_type']),
				$this->parseNode($arr['return_type']),
			),
			'integer' => new IntegerTypeTerm($this->parseNode($arr['range'])),
			'intersection' => new IntersectionTypeTerm(
				... array_map($this->parseNode(...), $arr['types'])
			),
			'map' => new MapTypeTerm(
				$this->parseNode($arr['item_type']),
				$this->parseNode($arr['length_range'])
			),
			'mutable' => new MutableTypeTerm($this->parseNode($arr['ref_type'])),
			'nothing' => new NothingTypeTerm,
			'real' => new RealTypeTerm($this->parseNode($arr['range'])),
			'record' => new RecordTypeTerm(
				... array_map($this->parseNode(...), $arr['types'])
			),
			'string' => new StringTypeTerm(
				$this->parseNode($arr['length_range'])
			),
			'true' => new TrueTypeTerm,
			'tuple' => new TupleTypeTerm(
				... array_map($this->parseNode(...), $arr['types'])
			),
			'type' => new TypeTypeTerm($this->parseNode($arr['ref_type'])),
			'type_name' => new TypeNameTerm(
				new TypeNameIdentifier($arr['type_name'] ?? $arr['value'])
			),
			'union' => new UnionTypeTerm(
				... array_map($this->parseNode(...), $arr['types'])
			),
			//default => print_r($arr)
		};
	}

	private function parseRange(array $arr): Range {
		return match($arr['type']) {
			'integer_range' => new IntegerRange(
				$arr['min_value'] === '-infinity' ? MinusInfinity::value : $arr['min_value'],
				$arr['max_value'] === '+infinity' ? PlusInfinity::value : $arr['max_value']
			),
			'real_range' => new RealRange(
				$arr['min_value'] === '-infinity' ? MinusInfinity::value : $arr['min_value'],
				$arr['max_value'] === '+infinity' ? PlusInfinity::value : $arr['max_value']
			),
			'length_range' => new LengthRange(
				$arr['min_length'],
				$arr['max_length'] === '+infinity' ? PlusInfinity::value : $arr['max_length']
			)
		};
	}

	private function parseLiteral(array $arr): BooleanLiteral|IntegerLiteral|NullLiteral|RealLiteral|StringLiteral {
		return match($arr['type']) {
			'boolean' => new BooleanLiteral($arr['value']),
			'integer' => new IntegerLiteral($arr['value']),
			'null' => new NullLiteral,
			'real' => new RealLiteral($arr['value']),
			'string' => new StringLiteral($arr['value']),
		};
	}

	private function parseConstant(array $arr): Constant {
		return match($arr['type']) {
			'literal' => new LiteralConstant($this->parseNode($arr['literal'])),
			'enumeration_value' => new EnumerationConstant(
				new TypeNameIdentifier($arr['enumeration']),
				new EnumValueIdentifier($arr['value'])
			),
			'function' => new FunctionConstant(
				$this->parseNode($arr['value']['parameter_type']),
				$this->parseFunctionReturnTypeTerm($arr['value']['return_type']),
				$this->parseNode($arr['value']['function_body']),
			),
			'type' => new TypeConstant($this->parseNode($arr['type_value'])),
			'record' => new RecordConstant(... array_map($this->parseNode(...), $arr['items'])),
			'tuple' => new TupleConstant(... array_map($this->parseNode(...), $arr['items'])),
		};
	}

}