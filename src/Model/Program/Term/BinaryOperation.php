<?php

namespace Cast\Model\Program\Term;

enum BinaryOperation: string {
	case plus = '+';
	case minus = '-';
	case power = '**';
	case multiplication = '*';
	case integerDivision = '//';
	case division = '/';
	case modulo = '%';

	case logicOr = '||';
	case logicAnd = '&&';

	case bitwiseOr = '|';
	case bitwiseAnd = '&';
	case bitwiseXor = '^';

	case subtype = '<:';
	//case assignment = '=';

	case lessThan = '<';
	case lessThanEqual = '<=';
	case greaterThan = '>';
	case greaterThanEqual = '>=';
	case equal = '==';
	case notEqual = '!=';
}