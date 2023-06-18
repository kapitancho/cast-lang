<?php

namespace Cast\Model\Program\Term;

enum UnaryOperation: string {
	case plus = '+';
	case minus = '-';

	case bitwiseNot = '~';
	case logicNot = '!';
}