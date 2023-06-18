<?php

namespace Cast\Test\Service\Program;

use Cast\Model\Program\Identifier\ModuleNameIdentifier;
use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\ModuleExpression;
use Cast\Model\Program\Program;
use Cast\Model\Program\ProgramModule;
use Cast\Model\Program\Term\Expression\TypeDefinitionTerm;
use Cast\Model\Program\Type\TypeNameTerm;
use Cast\Model\Program\TypeDefinition\AliasTypeDefinitionTerm;
use Cast\Service\Program\Builder\BuilderException;
use Cast\Service\Program\Builder\FromArraySourceBuilder;
use PHPUnit\Framework\TestCase;

final class ProgramTest extends TestCase {

	private readonly Program $program;

	protected function setUp(): void {
		parent::setUp();
		$this->program = new Program(
			new ProgramModule(
				new ModuleNameIdentifier("SurveyTemplate"),
				new ModuleExpression(
					new TypeDefinitionTerm(
						new AliasTypeDefinitionTerm(
							new TypeNameIdentifier("SurveyId"),
							new TypeNameTerm(new TypeNameIdentifier("SurveyIdX"))
						)
					)
				)
			)
		);
	}

	public function testToIdentity(): void {
		$program = json_encode($this->program);
		self::assertEquals($this->program, (new FromArraySourceBuilder)->build(
			json_decode($program, true)
		));
	}

	public function testInvalidInput(): void {
		$this->expectException(BuilderException::class);
		(new FromArraySourceBuilder)->build(['node' => 'x']);
	}
}