<?php

namespace Cast\Service\Registry;

use Cast\Model\Context\VariablePair;
use Cast\Model\Context\VariableScope;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Expression\FunctionCallExpression;
use Cast\Model\Program\Expression\PropertyAccessExpression;
use Cast\Model\Program\Expression\SequenceExpression;
use Cast\Model\Program\Expression\VariableNameExpression;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\FunctionType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Expression\AnalyserExpressionSource;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\TypeChainGenerator;
use Cast\Service\Type\UnionTypeNormalizer;

final readonly class CastBasedMethodFinder implements MethodFinder {
	public function __construct(
		private TypeAssistant $typeAssistant,
		private CastRegistry $castRegistry,
		private UnionTypeNormalizer $unionTypeNormalizer,
		private SubtypeRelationChecker $subtypeRelationChecker,
		private TypeChainGenerator $typeChainGenerator,
		private AnalyserExpressionSource $analyserExpressionSource,
	) {}

	public function findMethodFor(
		Type                   $targetType,
		PropertyNameIdentifier $methodName,
		Type                   $parameterType,
		SubExpressionAnalyser|null  $subExpressionAnalyser
	): FunctionValue|NoMethodAvailable {
		$foundCastToType = null;
		$foundCastExpression = null;
		$prop = null;
		foreach($this->typeChainGenerator->generateTypeChain($targetType) as $fromType) {
			foreach($this->castRegistry->getAllCastsFrom(
				$this->typeAssistant->closestTypeName($fromType)
			) as $castExpression) {
				$castToType = $this->typeAssistant->typeByName($castExpression->castToType);
				if ($castExpression->castToType->identifier === 'Any') {
					if ($subExpressionAnalyser) {
						$castToType = $subExpressionAnalyser->analyse(
							$castExpression->functionBody,
							VariableScope::fromVariablePairs(
								new VariablePair(
									new VariableNameIdentifier('$'),
									$fromType
								)
							)
						)->expressionType;
					} else {
						$castToType = $this->analyserExpressionSource->getExpressionResult(
							$castExpression->functionBody
						)->expressionType;
					}
				}

				$checkTypes = $castToType instanceof IntersectionType ?
					$castToType->types : [$castToType];
				foreach($checkTypes as $checkType) {
					$checkType = $this->typeAssistant->toBasicType($checkType);
					if ($checkType instanceof RecordType) {
						$prop = $checkType->propertyByKey($methodName->identifier);
						if ($prop) {
							$foundCastExpression = $castExpression;
							$foundCastToType = $castToType;
							break 3;
						}
					}
				}
			}
		}
		if (!$foundCastExpression) {
			return NoMethodAvailable::value;
		}
		if (!($prop instanceof FunctionType)) {
			throw new AnalyserException(
				sprintf("The expression %s->%s is not a function",
					$targetType, $methodName)
			);
		}

		$castToTypeX = $this->typeAssistant->followAliases($foundCastToType);

		if (!$this->subtypeRelationChecker->isSubtype($parameterType, $prop->parameterType)) {
			throw new AnalyserException(
				sprintf(
					"Cannot pass a parameter of type %s to a function expecting a parameter of type %s",
					$parameterType,
					$prop->parameterType,
				)
			);
		}
		return new FunctionValue(
			$prop->parameterType,
			new FunctionReturnType(
				$prop->returnType->returnType,
				$this->unionTypeNormalizer->normalizeErrorType(
					$prop->returnType->errorType,
					$foundCastExpression->errorType
				)
			),
			new FunctionBodyExpression(
				new SequenceExpression(
					new FunctionCallExpression(
						new PropertyAccessExpression(
							new SequenceExpression(
								$foundCastExpression->functionBody->body
							),
							$methodName
						),
						new VariableNameExpression(
							new VariableNameIdentifier('#')
						)
					)
				)
			),
		);
	}
}