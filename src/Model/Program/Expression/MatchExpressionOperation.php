<?php

namespace Cast\Model\Program\Expression;

enum MatchExpressionOperation {
	case equals;
	case isSubtypeOf;
}