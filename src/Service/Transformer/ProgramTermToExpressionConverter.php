<?php

namespace Cast\Service\Transformer;

use Cast\Model\Program\Expression\CatchExpression;
use Cast\Model\Program\Expression\ConstantExpression;
use Cast\Model\Program\Expression\ConstructorCallExpression;
use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Expression\FunctionCallExpression;
use Cast\Model\Program\Expression\LoopExpression;
use Cast\Model\Program\Expression\MatchExpression;
use Cast\Model\Program\Expression\MatchExpressionOperation;
use Cast\Model\Program\Expression\MatchPairExpression;
use Cast\Model\Program\Expression\MethodCallExpression;
use Cast\Model\Program\Expression\NativeCodeExpression;
use Cast\Model\Program\Expression\PropertyAccessExpression;
use Cast\Model\Program\Expression\RecordExpression;
use Cast\Model\Program\Expression\ReturnExpression;
use Cast\Model\Program\Expression\SequenceExpression;
use Cast\Model\Program\Expression\ThrowExpression;
use Cast\Model\Program\Expression\TupleExpression;
use Cast\Model\Program\Expression\VariableAssignmentExpression;
use Cast\Model\Program\Expression\VariableNameExpression;
use Cast\Model\Program\Identifier\PropertyNameIdentifier;
use Cast\Model\Program\Literal\BooleanLiteral;
use Cast\Model\Program\Term\BinaryTerm;
use Cast\Model\Program\Term\CatchTerm;
use Cast\Model\Program\Term\ConstantTerm;
use Cast\Model\Program\Term\ConstructorCallTerm;
use Cast\Model\Program\Term\DefaultMatchTerm;
use Cast\Model\Program\Term\FunctionCallTerm;
use Cast\Model\Program\Term\LoopTerm;
use Cast\Model\Program\Term\MatchIfTerm;
use Cast\Model\Program\Term\MatchPairTerm;
use Cast\Model\Program\Term\MatchTrueTerm;
use Cast\Model\Program\Term\MatchTypePairTerm;
use Cast\Model\Program\Term\MatchTypeTerm;
use Cast\Model\Program\Term\MatchValueTerm;
use Cast\Model\Program\Term\MethodCallTerm;
use Cast\Model\Program\Term\NativeCodeTerm;
use Cast\Model\Program\Term\PropertyAccessTerm;
use Cast\Model\Program\Term\RecordTerm;
use Cast\Model\Program\Term\ReturnTerm;
use Cast\Model\Program\Term\SequenceTerm;
use Cast\Model\Program\Term\Term;
use Cast\Model\Program\Term\ThrowTerm;
use Cast\Model\Program\Term\TupleTerm;
use Cast\Model\Program\Term\UnaryTerm;
use Cast\Model\Program\Term\VariableAssignmentTerm;
use Cast\Model\Program\Term\VariableNameTerm;
use Cast\Model\Runtime\Type\AnyType;
use Cast\Model\Runtime\Type\NullType;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Model\Runtime\Value\TypeValue;

final readonly class ProgramTermToExpressionConverter implements TermToExpressionConverter {

	public function __construct(
		private ConstantToValueConverter $constantToValueConverter,
		private TermToTypeTransformer $termToTypeTransformer,
	) {}

	private function trueExpression(): Expression {
		return new ConstantExpression(
			new LiteralValue(new BooleanLiteral(true))
		);
	}

	private function falseExpression(): Expression {
		return new ConstantExpression(
			new LiteralValue(new BooleanLiteral(false))
		);
	}

	public function termToExpression(Term $term): Expression {
		return match($term::class) {
			BinaryTerm::class => new MethodCallExpression(
				$this->termToExpression($term->firstExpression),
				new PropertyNameIdentifier("binary" . ucfirst($term->operation->name)),
					$this->termToExpression($term->secondExpression),
			),
			CatchTerm::class => new CatchExpression(
				$this->termToExpression($term->catchTarget), $term->anyType
			),
			ConstantTerm::class => new ConstantExpression(
				$this->constantToValueConverter->convertConstantToValue($term->constant)
			),
			ConstructorCallTerm::class => new ConstructorCallExpression(
				$term->type->typeName,
				$this->termToExpression($term->parameter)
			),
			FunctionCallTerm::class => new FunctionCallExpression(
				$this->termToExpression($term->target),
				$this->termToExpression($term->parameter),
			),
			LoopTerm::class => new LoopExpression(
				new MethodCallExpression(
					$this->termToExpression($term->checkExpression),
					new PropertyNameIdentifier('asBoolean'),
					ConstantExpression::emptyExpression()
				),
				$term->loopExpressionType,
				$this->termToExpression($term->loopExpression)
			),
			MatchIfTerm::class => new MatchExpression(
				new MethodCallExpression(
					$this->termToExpression($term->target),
					new PropertyNameIdentifier('asBoolean'),
					ConstantExpression::emptyExpression()
				),
				MatchExpressionOperation::equals,
				new MatchPairExpression(
					$this->trueExpression(),
					$this->termToExpression($term->trueExpression),
				),
				new MatchPairExpression(
					$this->falseExpression(),
					$this->termToExpression($term->falseExpression),
				),
			),
			MatchTrueTerm::class => new MatchExpression(
				$this->trueExpression(),
				MatchExpressionOperation::equals,
				... array_map(
					fn(MatchPairTerm|DefaultMatchTerm $expression): MatchPairExpression
						=> new MatchPairExpression(
							match($expression::class) {
								MatchPairTerm::class =>
									new MethodCallExpression(
										$this->termToExpression($expression->matchedExpression),
										new PropertyNameIdentifier('asBoolean'),
										ConstantExpression::emptyExpression()
									),
								DefaultMatchTerm::class => $this->trueExpression()
							},
							$this->termToExpression($expression->valueExpression)
						),
					$term->parameters
				)
			),
			MatchTypeTerm::class => new MatchExpression(
				$this->termToExpression($term->target),
				MatchExpressionOperation::isSubtypeOf,
				... array_map(
					fn(MatchTypePairTerm|DefaultMatchTerm $expression): MatchPairExpression
						=> new MatchPairExpression(
							match($expression::class) {
								MatchTypePairTerm::class => new ConstantExpression(
									new TypeValue(
										$this->termToTypeTransformer->termToType($expression->matchedExpression)
									)
								),
								DefaultMatchTerm::class => new ConstantExpression(
									new TypeValue(
										new AnyType
									)
								)
							},
							$this->termToExpression($expression->valueExpression)
						),
					$term->parameters
				)
			),
			MatchValueTerm::class => new MatchExpression(
				$t = $this->termToExpression($term->target),
				MatchExpressionOperation::equals,
				... array_map(
					fn(MatchPairTerm|DefaultMatchTerm $expression): MatchPairExpression
						=> new MatchPairExpression(
							match($expression::class) {
								MatchPairTerm::class => $this->termToExpression($expression->matchedExpression),
								DefaultMatchTerm::class => $t
							},
							$this->termToExpression($expression->valueExpression)
						),
					$term->parameters
				)
			),
			MethodCallTerm::class => new MethodCallExpression(
				$this->termToExpression($term->target),
				$term->methodName,
				$this->termToExpression($term->parameter),
			),

			NativeCodeTerm::class => new NativeCodeExpression($term->id, new NullType),
			PropertyAccessTerm::class => new PropertyAccessExpression(
				$this->termToExpression($term->target), $term->propertyName
			),
			SequenceTerm::class => new SequenceExpression(
				... array_map($this->termToExpression(...), $term->terms)
			),
			RecordTerm::class => new RecordExpression(
				... array_map($this->termToExpression(...), $term->values)
			),
			ReturnTerm::class => new ReturnExpression(
				$this->termToExpression($term->returnedValue)
			),
			ThrowTerm::class => new ThrowExpression(
				$this->termToExpression($term->thrownValue)
			),
			TupleTerm::class => new TupleExpression(
				... array_map($this->termToExpression(...), $term->values)
			),
			UnaryTerm::class => new MethodCallExpression(
				$this->termToExpression($term->expression),
				new PropertyNameIdentifier("unary" . ucfirst($term->operation->name)),
				ConstantExpression::emptyExpression(),
			),
			VariableNameTerm::class => new VariableNameExpression($term->variableName),
			VariableAssignmentTerm::class => new VariableAssignmentExpression(
				$term->variableName,
				$this->termToExpression($term->value)
			),
			default => null
		};
	}
}