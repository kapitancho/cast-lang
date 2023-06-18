<?php

namespace Cast\Service\Transformer;

use Cast\Model\Program\Expression\Expression;
use Cast\Model\Program\Term\Term;

interface TermToExpressionConverter {
	public function termToExpression(Term $term): Expression;
}