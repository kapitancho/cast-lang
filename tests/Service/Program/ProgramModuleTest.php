<?php

namespace Cast\Test\Service\Program;

use Cast\Model\Program\Constant\LiteralConstant;
use Cast\Model\Program\Identifier\EnumValueIdentifier;
use Cast\Model\Program\Identifier\ModuleNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Program\Literal\IntegerLiteral;
use Cast\Model\Program\Literal\NullLiteral;
use Cast\Model\Program\ModuleExpression;
use Cast\Model\Program\ProgramModule;
use Cast\Model\Program\Range\IntegerRange;
use Cast\Model\Program\Term\ConstantTerm;
use Cast\Model\Program\Term\Expression\ConstantAssignmentTerm;
use Cast\Model\Program\Term\Expression\TypeCastTerm;
use Cast\Model\Program\Term\Expression\TypeDefinitionTerm;
use Cast\Model\Program\Term\FunctionBodyTerm;
use Cast\Model\Program\Type\IntegerTypeTerm;
use Cast\Model\Program\Type\TypeNameTerm;
use Cast\Model\Program\TypeDefinition\AliasTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\EnumerationTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\SubtypeDefinitionTerm;
use PHPUnit\Framework\TestCase;

final class ProgramModuleTest extends TestCase {

	private readonly ProgramModule $programModule;

	protected function setUp(): void {
		parent::setUp();
		$this->programModule = new ProgramModule(
			new ModuleNameIdentifier("SurveyTemplate"),

			new ModuleExpression(
				new TypeDefinitionTerm(
					new AliasTypeDefinitionTerm(
						new TypeNameIdentifier("SurveyId"),
						new TypeNameTerm(new TypeNameIdentifier("SurveyIdX"))
					)
				)
			),
			new ModuleExpression(
				new TypeDefinitionTerm(
					new SubtypeDefinitionTerm(
						new TypeNameIdentifier("TemplateId"),
						new TypeNameTerm(new TypeNameIdentifier("Uuid")),
						new FunctionBodyTerm(
							new ConstantTerm(
								new LiteralConstant(new NullLiteral)
							))
					),
				)
			),
			new ModuleExpression(
				new TypeDefinitionTerm(
					new AliasTypeDefinitionTerm(
						new TypeNameIdentifier("SurveyNumber"),
						new IntegerTypeTerm(new IntegerRange(1, 9999))
					)
				)
			),
			new ModuleExpression(
				new ConstantAssignmentTerm(
					new VariableNameIdentifier("maxSurveyDuration"),
					new LiteralConstant(new IntegerLiteral(60))
				)
			),
			new ModuleExpression(
				new TypeCastTerm(
					new TypeNameTerm(new TypeNameIdentifier("Survey")),
					new TypeNameTerm(new TypeNameIdentifier("Boolean")),
					new FunctionBodyTerm(
						new ConstantTerm(
							new LiteralConstant(new NullLiteral)
						)
					)
				)
			),
			new ModuleExpression(
				new TypeDefinitionTerm(
					new EnumerationTypeDefinitionTerm(
						new TypeNameIdentifier("SurveyStatus"),
						new EnumValueIdentifier("Draft"),
						new EnumValueIdentifier("Active"),
						new EnumValueIdentifier("Completed"),
						new EnumValueIdentifier("Cancelled"),
					)
				)
			)
		);
	}

	public function testToString(): void {
		self::assertEquals(str_replace("\r\n", "\n", <<<CODE
		-> SurveyTemplate;
		
		SurveyId = SurveyIdX;
		TemplateId <: Uuid;
		SurveyNumber = Integer<1..9999>;
		maxSurveyDuration = 60;
		Survey ==> Boolean :: null;
		SurveyStatus = :[Draft, Active, Completed, Cancelled];

		CODE), (string)$this->programModule);
	}
}