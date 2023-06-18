<?php

namespace Cast\Service\Registry;

use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Expression\MatchExpression;
use Cast\Model\Program\Expression\MatchExpressionOperation;
use Cast\Model\Program\Expression\MatchPairExpression;
use Cast\Model\Program\Expression\VariableNameExpression;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\UnionTypeNormalizer;

final readonly class CompositeTypeMethodFinder implements MethodFinder {
	public function __construct(
		private TypeAssistant $typeAssistant,
		private UnionTypeNormalizer $unionTypeNormalizer,
		private MethodFinder $methodFinder
	) {}

	public function findMethodFor(
		Type                   $targetType,
		PropertyNameIdentifier $methodName,
		Type                   $parameterType,
		SubExpressionAnalyser|null  $subExpressionAnalyser
	): FunctionValue|NoMethodAvailable {
		$targetTypeX = $this->typeAssistant->followAliases($targetType);
		$parameterType = $this->typeAssistant->followAliases($parameterType);

		if ($targetType instanceof UnionType) {
			$typePairs = [];
			$returnTypes = [];
			$errorTypes = [];
			foreach($targetType->types as $unionType) {
				$fn = $this->findMethodFor(
					$unionType,
					$methodName,
					$parameterType,
					$subExpressionAnalyser
				);
				if ($fn === NoMethodAvailable::value) {
					return NoMethodAvailable::value;
				}
				$typePairs[] = new MatchPairExpression(
					new ConstantExpression(
						new TypeValue($unionType)
					),
					$fn->functionBody->body,
				);
				$returnTypes[] = $fn->returnType->returnType;
				$errorTypes[] = $fn->returnType->errorType;
			}
			$matchTypeExpression = new MatchExpression(
				new VariableNameExpression(new VariableNameIdentifier('$')),
				MatchExpressionOperation::isSubtypeOf,
				... $typePairs
			);
			$returnType = $this->unionTypeNormalizer->normalize(... $returnTypes);
			$errorType = $this->unionTypeNormalizer->normalizeErrorType(... $errorTypes);
			return new FunctionValue(
				new NullType,
				new FunctionReturnType($returnType, $errorType),
				new FunctionBodyExpression(
					$matchTypeExpression
				)
			);
		}
		if ($targetTypeX instanceof IntersectionType) {
			foreach($targetTypeX->types as $targetTypeItem) {
				if (($subResult = $this->findMethodFor(
					$targetTypeItem, $methodName, $parameterType, $subExpressionAnalyser
				)) !== NoMethodAvailable::value) {
					return $subResult;
				}
			}
			return NoMethodAvailable::value;
		}
		return $this->methodFinder->findMethodFor(
			$targetType, $methodName, $parameterType, $subExpressionAnalyser
		);
	}
}