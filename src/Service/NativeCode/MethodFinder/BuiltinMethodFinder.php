<?php

namespace Cast\Service\NativeCode\MethodFinder;

use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\FunctionBodyExpression;
use Cast\Model\Program\Expression\MatchExpression;
use Cast\Model\Program\Expression\MatchExpressionOperation;
use Cast\Model\Program\Expression\MatchPairExpression;
use Cast\Model\Program\Expression\VariableNameExpression;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Identifier\VariableNameIdentifier;
use Cast\Model\Runtime\Type\AliasType;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\ArrayType;
use Cast\Model\Runtime\Type\BooleanType;
use Cast\Model\Runtime\Type\EnumerationType;
use Cast\Model\Runtime\Type\FunctionReturnType;
use Cast\Model\Runtime\Type\IntegerType;
use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\MapType;
use Cast\Model\Runtime\Type\MutableType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Type\RealType;
use Cast\Model\Runtime\Type\StringType;
use Cast\Model\Runtime\Type\SubtypeType;
use Cast\Model\Runtime\Type\Type;
use Cast\Model\Runtime\Type\TypeType;
use Cast\Model\Runtime\Type\UnionType;
use Cast\Model\Runtime\Value\FunctionValue;
use Cast\Model\Runtime\Value\TypeValue;
use Cast\Service\Expression\SubExpressionAnalyser;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Registry\MethodFinder;
use Cast\Service\Registry\NoMethodAvailable;
use Cast\Service\Type\ExpressionToTypeConverter;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;
use Cast\Service\Type\TypeChainGenerator;
use Cast\Service\Type\UnionTypeNormalizer;

final readonly class BuiltinMethodFinder implements MethodFinder {
	private AliasMethodFinder $aliasMethodFinder;
	private AnyMethodFinder $anyMethodFinder;
	private ArrayMethodFinder $arrayMethodFinder;
	private BooleanMethodFinder $booleanMethodFinder;
	private EnumMethodFinder $enumMethodFinder;
	private IntegerMethodFinder $integerMethodFinder;
	private MapMethodFinder $mapMethodFinder;
	private MutableMethodFinder $mutableMethodFinder;
	private RealMethodFinder $realMethodFinder;
	private StringMethodFinder $stringMethodFinder;
	private SubtypeMethodFinder $subtypeMethodFinder;
	private TypeMethodFinder $typeMethodFinder;

	public function __construct(
		private TypeAssistant $typeAssistant,
		private UnionTypeNormalizer $unionTypeNormalizer,
		private SubtypeRelationChecker $subtypeRelationChecker,
		private CastRegistry $castRegistry,
		private TypeChainGenerator $typeChainGenerator,
		private ExpressionToTypeConverter $expressionToTypeConverter,
	) {
		$this->anyMethodFinder = new AnyMethodFinder(
			$this->typeAssistant,
			$this->subtypeRelationChecker,
			$this->castRegistry,
			$this->expressionToTypeConverter
		);
		$this->arrayMethodFinder = new ArrayMethodFinder(
			$this->typeAssistant,
			$this->unionTypeNormalizer,
			$this->subtypeRelationChecker
		);
		$this->booleanMethodFinder = new BooleanMethodFinder;
		$this->integerMethodFinder = new IntegerMethodFinder($this->typeAssistant);
		$this->enumMethodFinder = new EnumMethodFinder;
		$this->mapMethodFinder = new MapMethodFinder(
			$this->typeAssistant,
			$this->unionTypeNormalizer,
			$this->subtypeRelationChecker
		);
		$this->mutableMethodFinder = new MutableMethodFinder(
			$this->subtypeRelationChecker
		);
		$this->realMethodFinder = new RealMethodFinder($this->typeAssistant);
		$this->stringMethodFinder = new StringMethodFinder(
			$this->typeAssistant,
			$this->unionTypeNormalizer,
			$this->subtypeRelationChecker
		);
		$this->subtypeMethodFinder = new SubtypeMethodFinder(
			$this->typeAssistant,
			$this->castRegistry,
			$this->subtypeRelationChecker,
		);
		$this->aliasMethodFinder = new AliasMethodFinder(
			$this->typeAssistant,
			$this->castRegistry,
			$this->subtypeRelationChecker,
		);
		$this->typeMethodFinder = new TypeMethodFinder(
			$this->typeAssistant,
		);
	}

	public function findMethodFor(
		Type                   $targetType,
		PropertyNameIdentifier $methodName,
		Type                   $parameterType,
		SubExpressionAnalyser|null  $subExpressionAnalyser
	): FunctionValue|NoMethodAvailable {
		$targetTypeX = $this->typeAssistant->followAliases($targetType);
		$parameterType = $this->typeAssistant->followAliases($parameterType);

		foreach($this->typeChainGenerator->generateTypeChain($targetType) as $targetTypeCandidate) {
			/**
			 * @var ByTypeMethodFinder $finder
			 */
			$finder = match($targetTypeCandidate::class) {
				AnyType::class => $this->anyMethodFinder,
				ArrayType::class => $this->arrayMethodFinder,
				BooleanType::class => $this->booleanMethodFinder,
				EnumerationType::class => $this->enumMethodFinder,
				IntegerType::class => $this->integerMethodFinder,
				MapType::class => $this->mapMethodFinder,
				MutableType::class => $this->mutableMethodFinder,
				RealType::class => $this->realMethodFinder,
				StringType::class => $this->stringMethodFinder,
				TypeType::class => $this->typeMethodFinder,
				default => match(true) {
					$targetTypeCandidate instanceof AliasType => $this->aliasMethodFinder,
					$targetTypeCandidate instanceof SubtypeType => $this->subtypeMethodFinder,
					default => null
				}
			};
			if ($finder) {
				$fn = $finder->findMethodFor(
					$targetTypeX,
					$targetTypeCandidate, $methodName, $parameterType);
				if ($fn !== NoMethodAvailable::value) {
					return $fn;
				}
			}
		}
		return NoMethodAvailable::value;
	}
}