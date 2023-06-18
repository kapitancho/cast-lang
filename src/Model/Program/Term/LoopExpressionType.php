<?php

namespace Cast\Model\Program\Term;

enum LoopExpressionType: string {
	case While = 'while';
	case DoWhile = 'do-while';

	public function sign(): string {
		return match($this) {
			self::While => '?*',
			self::DoWhile => '?+',
		};
	}
}