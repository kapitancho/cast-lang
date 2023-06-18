<?php

namespace Cast\Service\NativeCode;

use Cast\Model\Program\Identifier\TypeNameIdentifier;
use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Runtime\Value\DictValue;
use Cast\Model\Runtime\Value\EnumerationValue;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\SubtypeValue;
use Cast\Model\Runtime\Value\Value;
use Cast\Service\Execution\Flow\ThrowResult;
use Cast\Service\Expression\FunctionExecutor;
use Cast\Service\Expression\SubExpressionExecutor;
use Cast\Service\Registry\CastRegistry;
use Cast\Service\Registry\NoCastAvailable;
use Cast\Service\Type\SubtypeRelationChecker;
use Cast\Service\Type\TypeAssistant;

final readonly class NestedDehydrator implements Dehydrator {

	public function __construct(
		private TypeAssistant $typeAssistant,
		private SubtypeRelationChecker $subtypeRelationChecker,
		private CastRegistry $castRegistry,
		private FunctionExecutor $functionExecutor,
		private SubExpressionExecutor $subExpressionExecutor
	) {}

	/** @throws DehydrationException */
	public function dehydrate(Value $value, string $hydrationPath): Value {
		/** @var callable-string $fn */
		$fn = match($value::class) {
			SubtypeValue::class => $this->dehydrateSubtypeValue(...),
			EnumerationValue::class => $this->dehydrateEnumerationValue(...),
			DictValue::class => $this->dehydrateDictValue(...),
			ListValue::class => $this->dehydrateListValue(...),
			LiteralValue::class => $this->dehidrateLiteralValue(...),
		};
		return $fn($value, $hydrationPath);
	}

	private function dehydrateSubtypeValue(SubtypeValue $value, string $hydrationPath): Value {
		$cast = $this->castRegistry->getCast(
			$value->typeName,
			new TypeNameIdentifier('JsonValue'),
		);
		if ($cast !== NoCastAvailable::value) {
			try {
				/** @var EnumerationValue */
				return $this->functionExecutor->executeCastFunction(
					$this->subExpressionExecutor,
					$cast,
					$value
				);
			} catch (ThrowResult $throwResult) {
				throw new HydrationException(
					$value,
					$hydrationPath,
					sprintf("Subtype dehydration failed: %s",
						$throwResult->v
					)
				);
			}
		}
		return $this->dehydrate($value->baseValue, $hydrationPath);
	}

	private function dehydrateEnumerationValue(EnumerationValue $value, string $hydrationPath): Value {
		$cast = $this->castRegistry->getCast(
			$value->enumTypeName,
			new TypeNameIdentifier('JsonValue'),
		);
		if ($cast !== NoCastAvailable::value) {
			try {
				/** @var EnumerationValue */
				return $this->functionExecutor->executeCastFunction(
					$this->subExpressionExecutor,
					$cast,
					$value
				);
			} catch (ThrowResult $throwResult) {
				throw new HydrationException(
					$value,
					$hydrationPath,
					sprintf("Enumeration dehydration failed: %s",
						$throwResult->v
					)
				);
			}
		}
		return new LiteralValue(new StringLiteral($value->enumValue->identifier));
	}

	private function dehydrateDictValue(DictValue $value, string $hydrationPath): Value {
		$result = [];
		foreach($value->items as $key => $itemValue) {
			$result[$key] = $this->dehydrate($itemValue, "{$hydrationPath}.$key");
		}
		return new DictValue( $result);
	}

	private function dehydrateListValue(ListValue $value, string $hydrationPath): Value {
		$result = [];
		foreach($value->items as $seq => $itemValue) {
			$result[$seq] = $this->dehydrate($itemValue, "{$hydrationPath}[$seq]");
		}
		return new ListValue(...  $result);
	}

	private function dehidrateLiteralValue(LiteralValue $value, string $hydrationPath): Value {
		return $value;
	}

}