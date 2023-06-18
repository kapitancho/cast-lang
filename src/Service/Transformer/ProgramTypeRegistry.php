<?php

namespace Cast\Service\Transformer;

use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Program;
use Cast\Model\Program\Term\Expression\TypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\AliasTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\EnumerationTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\SubtypeDefinitionTerm;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\Type;
use Cast\Service\Registry\AliasTypeProxy;
use Cast\Service\Registry\BuiltInTypeRegistry;
use Cast\Service\Registry\SubtypeTypeProxy;
use Cast\Service\Type\UnknownType;

final readonly class ProgramTypeRegistry implements TypeRegistry {
	/** @var array<Type> */
	private array $types;

	public function __construct(
		Program $program,
		BuiltInTypeRegistry $builtInTypeRegistry,
		private TermToTypeTransformer $termToTypeTransformer,
		private TermToExpressionConverter $termToExpressionConverter,
	) {
		$this->types = $this->buildTypeMapping(
			$program,
			$builtInTypeRegistry,
		);
	}

	private function convertTypeDefinitionTerm(
		EnumerationTypeDefinitionTerm|SubtypeDefinitionTerm|AliasTypeDefinitionTerm $typeDef
	): Type {
		return match($typeDef::class) {
			EnumerationTypeDefinitionTerm::class => new EnumerationType(
				$typeDef->typeName,
				... $typeDef->values
			),
			SubtypeDefinitionTerm::class => new SubtypeTypeProxy(
				$this->termToTypeTransformer,
				$typeDef->typeName,
				$typeDef->errorType,
				new FunctionBodyExpression(
					$this->termToExpressionConverter->termToExpression($typeDef->functionBody->body)
				),
				$typeDef->baseType,
			),
			AliasTypeDefinitionTerm::class => new AliasTypeProxy(
				$this->termToTypeTransformer,
				$typeDef->typeName,
				$typeDef->aliasedType,
			)
		};
	}

	/** @return array<Type> */
	private function buildTypeMapping(Program $program, BuiltInTypeRegistry $builtInTypeRegistry): array {
		$types = [];
		foreach($builtInTypeRegistry->getBuiltinTypes() as $builtinType) {
			$types[$builtinType->typeName->identifier] =
				$this->convertTypeDefinitionTerm($builtinType);
		}
		foreach($program->modules as $programModule) {
			foreach ($programModule->expressions() as $moduleExpression) {
				$expr = $moduleExpression->expression;
				if ($expr instanceof TypeDefinitionTerm) {
					$types[$expr->type->typeName->identifier] =
						$this->convertTypeDefinitionTerm($expr->type);
				}
			}
		}
		return $types;
	}

	/** @return Type[] */
	public function allTypes(): array {
		return $this->types;
	}

	/** @throws UnknownType */
	public function typeByName(TypeNameIdentifier $name): Type {
		return $this->types[$name->identifier] ?? UnknownType::withName($name);
	}
}