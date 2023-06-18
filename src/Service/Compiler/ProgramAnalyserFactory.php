<?php

namespace Cast\Service\Compiler;

use Cast\Service\Expression\ContextCodeExpressionAnalyser;
use Cast\Service\Transformer\TypeRegistry;
use Cast\Service\Type\SubtypeRelationChecker;

final readonly class ProgramAnalyserFactory {

	public function __construct(
		private TypeRegistry                  $typeByNameFinder,
		private ContextCodeExpressionAnalyser $codeExpressionAnalyser,
		private SubtypeRelationChecker        $subtypeRelationChecker,
	) {}

	public function getAnalyser(): ProgramAnalyser {
		return new ProgramAnalyser(
			new FunctionAnalyser(
				$this->codeExpressionAnalyser,
				$this->subtypeRelationChecker,
			),
			new TypeCastAnalyser(
				$this->typeByNameFinder,
				$this->codeExpressionAnalyser,
				$this->subtypeRelationChecker
			),
			new SubtypeAnalyser(
				$this->codeExpressionAnalyser,
				$this->subtypeRelationChecker
			)
		);

	}

}