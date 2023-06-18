<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Range\LengthRange;
use Cast\Model\Program\Range\PlusInfinity;
use Cast\Model\Program\Term\FunctionBodyTerm;
use Cast\Model\Program\Type\AnyTypeTerm;
use Cast\Model\Program\Type\ArrayTypeTerm;
use Cast\Model\Program\Type\BooleanTypeTerm;
use Cast\Model\Program\Type\ErrorTypeTerm;
use Cast\Model\Program\Type\FalseTypeTerm;
use Cast\Model\Program\Type\FunctionReturnTypeTerm;
use Cast\Model\Program\Type\FunctionTypeTerm;
use Cast\Model\Program\Type\IntegerTypeTerm;
use Cast\Model\Program\Type\MapTypeTerm;
use Cast\Model\Program\Type\MutableTypeTerm;
use Cast\Model\Program\Type\NothingTypeTerm;
use Cast\Model\Program\Type\NullTypeTerm;
use Cast\Model\Program\Type\RealTypeTerm;
use Cast\Model\Program\Type\RecordTypeTerm;
use Cast\Model\Program\Type\StringTypeTerm;
use Cast\Model\Program\Type\TrueTypeTerm;
use Cast\Model\Program\Type\TypeNameTerm;
use Cast\Model\Program\Type\TypeTypeTerm;
use Cast\Model\Program\Type\UnionTypeTerm;
use Cast\Model\Program\TypeDefinition\AliasTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\EnumerationTypeDefinitionTerm;
use Cast\Model\Program\TypeDefinition\SubtypeDefinitionTerm;

final readonly class BuiltInTypeRegistry {

	private function getCoreTypes(): array {
		return [
			'Mutable' => new MutableTypeTerm(new AnyTypeTerm),
			'Integer' => IntegerTypeTerm::base(),
			'Real' => RealTypeTerm::base(),
			'String' => StringTypeTerm::base(),
			'Null' => new NullTypeTerm,
			'Boolean' => new BooleanTypeTerm,
			'True' => new TrueTypeTerm,
			'False' => new FalseTypeTerm,
			'Any' => new AnyTypeTerm,
			'Nothing' => new NothingTypeTerm,
			'Array' => ArrayTypeTerm::base(),
			'Map' => MapTypeTerm::base(),
			'Type' => new TypeTypeTerm(new AnyTypeTerm)
		];
	}

	/**
	 * @return array<AliasTypeDefinitionTerm|SubtypeDefinitionTerm|EnumerationTypeDefinitionTerm>
	 */
	public function getBuiltinTypes(): array {
		$result = [];
		foreach($this->getCoreTypes() as $name => $term) {
			$result[] = new AliasTypeDefinitionTerm(
				new TypeNameIdentifier($name),
				$term
			);
		}
		return $result + [];
	}
}