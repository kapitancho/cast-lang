<?php

namespace Cast\Service\Type;

use Cast\Model\Runtime\Type\IntersectionType;
use Cast\Model\Runtime\Type\RecordType;
use Cast\Model\Runtime\Type\TupleType;
use Cast\Model\Runtime\Type\Type;

final readonly class TupleToRecordTypeConverter {

	public function toRecordType(TupleType $tupleType, RecordType $refRecordType): RecordType {
		$pTypes = [];
		$abIndex = 0;
		foreach($refRecordType->types as $k => $v) {
			$pTypes[$k] = $tupleType->types[$abIndex++];
		}
		return new RecordType(... $pTypes);
	}

	public function tryToRecordType(Type $type, RecordType $refRecordType): Type {
		$types = $type instanceof IntersectionType ? $type->types : [$type];
		foreach($types as $typeCandidate) {
			if ($typeCandidate instanceof TupleType && count($typeCandidate->types) === count($refRecordType->types)) {
				return $this->toRecordType($typeCandidate, $refRecordType);
			}
		}
		return $type;
	}

}