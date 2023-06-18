<?php

require_once __DIR__ . '/../vendor/kapitancho/php-peg/autoloader.php';
use hafriedlander\Peg\Parser;

ini_set('error_reporting', E_NOTICE|E_WARNING|E_ERROR);

/**
 * This parser strictly matches the RFC822 standard. No characters outside the ASCII range 0-127 are allowed
 * @author Hamish Friedlander
 */
#[AllowDynamicProperties]
class Cast7 extends Parser\Basic {

/* whitespace-char: " " | "\t" | /\r/ | /\n/ */
protected $match_whitespace_char_typestack = array('whitespace_char');
function match_whitespace_char ($stack = array()) {
	$matchrule = "whitespace_char"; $result = $this->construct($matchrule, $matchrule, null);
	$_11 = NULL;
	do {
		$res_0 = $result;
		$pos_0 = $this->pos;
		if (substr($this->string,$this->pos,1) == ' ') {
			$this->pos += 1;
			$result["text"] .= ' ';
			$_11 = TRUE; break;
		}
		$result = $res_0;
		$this->pos = $pos_0;
		$_9 = NULL;
		do {
			$res_2 = $result;
			$pos_2 = $this->pos;
			if (( $subres = $this->literal( '\t' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_9 = TRUE; break;
			}
			$result = $res_2;
			$this->pos = $pos_2;
			$_7 = NULL;
			do {
				$res_4 = $result;
				$pos_4 = $this->pos;
				if (( $subres = $this->rx( '/\r/' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_7 = TRUE; break;
				}
				$result = $res_4;
				$this->pos = $pos_4;
				if (( $subres = $this->rx( '/\n/' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_7 = TRUE; break;
				}
				$result = $res_4;
				$this->pos = $pos_4;
				$_7 = FALSE; break;
			}
			while(0);
			if( $_7 === TRUE ) { $_9 = TRUE; break; }
			$result = $res_2;
			$this->pos = $pos_2;
			$_9 = FALSE; break;
		}
		while(0);
		if( $_9 === TRUE ) { $_11 = TRUE; break; }
		$result = $res_0;
		$this->pos = $pos_0;
		$_11 = FALSE; break;
	}
	while(0);
	if( $_11 === TRUE ) { return $this->finalise($result); }
	if( $_11 === FALSE) { return FALSE; }
}


/* whitespace: whitespace-char* */
protected $match_whitespace_typestack = array('whitespace');
function match_whitespace ($stack = array()) {
	$matchrule = "whitespace"; $result = $this->construct($matchrule, $matchrule, null);
	while (true) {
		$res_13 = $result;
		$pos_13 = $this->pos;
		$matcher = 'match_'.'whitespace_char'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_13;
			$this->pos = $pos_13;
			unset( $res_13 );
			unset( $pos_13 );
			break;
		}
	}
	return $this->finalise($result);
}


/* boolean-value: "true" | "false" */
protected $match_boolean_value_typestack = array('boolean_value');
function match_boolean_value ($stack = array()) {
	$matchrule = "boolean_value"; $result = $this->construct($matchrule, $matchrule, null);
	$_17 = NULL;
	do {
		$res_14 = $result;
		$pos_14 = $this->pos;
		if (( $subres = $this->literal( 'true' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_17 = TRUE; break;
		}
		$result = $res_14;
		$this->pos = $pos_14;
		if (( $subres = $this->literal( 'false' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_17 = TRUE; break;
		}
		$result = $res_14;
		$this->pos = $pos_14;
		$_17 = FALSE; break;
	}
	while(0);
	if( $_17 === TRUE ) { return $this->finalise($result); }
	if( $_17 === FALSE) { return FALSE; }
}


/* null-value: "null" */
protected $match_null_value_typestack = array('null_value');
function match_null_value ($stack = array()) {
	$matchrule = "null_value"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( 'null' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* non-negative-integer-value: /\d+/ */
protected $match_non_negative_integer_value_typestack = array('non_negative_integer_value');
function match_non_negative_integer_value ($stack = array()) {
	$matchrule = "non_negative_integer_value"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/\d+/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* integer-value: /-?\d+/ */
protected $match_integer_value_typestack = array('integer_value');
function match_integer_value ($stack = array()) {
	$matchrule = "integer_value"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/-?\d+/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* real-value: /-?\d+\.\d+/ */
protected $match_real_value_typestack = array('real_value');
function match_real_value ($stack = array()) {
	$matchrule = "real_value"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/-?\d+\.\d+/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* string-value: /\"[^"]*\"/ */
protected $match_string_value_typestack = array('string_value');
function match_string_value ($stack = array()) {
	$matchrule = "string_value"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/\"[^"]*\"/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* enum-type-name: /[A-Z][a-zA-Z0-9]{0,}/ */
protected $match_enum_type_name_typestack = array('enum_type_name');
function match_enum_type_name ($stack = array()) {
	$matchrule = "enum_type_name"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[A-Z][a-zA-Z0-9]{0,}/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* enum-type-value: /[A-Z][a-zA-Z0-9]{0,}/ */
protected $match_enum_type_value_typestack = array('enum_type_value');
function match_enum_type_value ($stack = array()) {
	$matchrule = "enum_type_value"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[A-Z][a-zA-Z0-9]{0,}/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* map-key: /[a-zA-Z0-9_]+/ */
protected $match_map_key_typestack = array('map_key');
function match_map_key ($stack = array()) {
	$matchrule = "map_key"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[a-zA-Z0-9_]+/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* variable-name: /(\#)?[a-z][a-zA-Z0-9]{0,}/ | "#0" | /(\#)[1-9][0-9]{0,}/ | "#" | "$" */
protected $match_variable_name_typestack = array('variable_name');
function match_variable_name ($stack = array()) {
	$matchrule = "variable_name"; $result = $this->construct($matchrule, $matchrule, null);
	$_42 = NULL;
	do {
		$res_27 = $result;
		$pos_27 = $this->pos;
		if (( $subres = $this->rx( '/(\#)?[a-z][a-zA-Z0-9]{0,}/' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_42 = TRUE; break;
		}
		$result = $res_27;
		$this->pos = $pos_27;
		$_40 = NULL;
		do {
			$res_29 = $result;
			$pos_29 = $this->pos;
			if (( $subres = $this->literal( '#0' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_40 = TRUE; break;
			}
			$result = $res_29;
			$this->pos = $pos_29;
			$_38 = NULL;
			do {
				$res_31 = $result;
				$pos_31 = $this->pos;
				if (( $subres = $this->rx( '/(\#)[1-9][0-9]{0,}/' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_38 = TRUE; break;
				}
				$result = $res_31;
				$this->pos = $pos_31;
				$_36 = NULL;
				do {
					$res_33 = $result;
					$pos_33 = $this->pos;
					if (substr($this->string,$this->pos,1) == '#') {
						$this->pos += 1;
						$result["text"] .= '#';
						$_36 = TRUE; break;
					}
					$result = $res_33;
					$this->pos = $pos_33;
					if (substr($this->string,$this->pos,1) == '$') {
						$this->pos += 1;
						$result["text"] .= '$';
						$_36 = TRUE; break;
					}
					$result = $res_33;
					$this->pos = $pos_33;
					$_36 = FALSE; break;
				}
				while(0);
				if( $_36 === TRUE ) { $_38 = TRUE; break; }
				$result = $res_31;
				$this->pos = $pos_31;
				$_38 = FALSE; break;
			}
			while(0);
			if( $_38 === TRUE ) { $_40 = TRUE; break; }
			$result = $res_29;
			$this->pos = $pos_29;
			$_40 = FALSE; break;
		}
		while(0);
		if( $_40 === TRUE ) { $_42 = TRUE; break; }
		$result = $res_27;
		$this->pos = $pos_27;
		$_42 = FALSE; break;
	}
	while(0);
	if( $_42 === TRUE ) { return $this->finalise($result); }
	if( $_42 === FALSE) { return FALSE; }
}


/* record-key: map-key | string-value */
protected $match_record_key_typestack = array('record_key');
function match_record_key ($stack = array()) {
	$matchrule = "record_key"; $result = $this->construct($matchrule, $matchrule, null);
	$_47 = NULL;
	do {
		$res_44 = $result;
		$pos_44 = $this->pos;
		$matcher = 'match_'.'map_key'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_47 = TRUE; break;
		}
		$result = $res_44;
		$this->pos = $pos_44;
		$matcher = 'match_'.'string_value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_47 = TRUE; break;
		}
		$result = $res_44;
		$this->pos = $pos_44;
		$_47 = FALSE; break;
	}
	while(0);
	if( $_47 === TRUE ) { return $this->finalise($result); }
	if( $_47 === FALSE) { return FALSE; }
}

public function record_key_map_key ( &$self, $sub ) {
		$self['key'] = $sub;
	}

public function record_key_string_value ( &$self, $sub ) {
		$self['key'] = substr($sub['text'], 1, -1);
	}

/* module-name-node: /[A-Z][a-zA-Z0-9]+/ */
protected $match_module_name_node_typestack = array('module_name_node');
function match_module_name_node ($stack = array()) {
	$matchrule = "module_name_node"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[A-Z][a-zA-Z0-9]+/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* minus-infinity: "-Infinity" */
protected $match_minus_infinity_typestack = array('minus_infinity');
function match_minus_infinity ($stack = array()) {
	$matchrule = "minus_infinity"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '-Infinity' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* plus-infinity: "+Infinity" */
protected $match_plus_infinity_typestack = array('plus_infinity');
function match_plus_infinity ($stack = array()) {
	$matchrule = "plus_infinity"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '+Infinity' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* integer-range-from: minus-infinity | integer-value */
protected $match_integer_range_from_typestack = array('integer_range_from');
function match_integer_range_from ($stack = array()) {
	$matchrule = "integer_range_from"; $result = $this->construct($matchrule, $matchrule, null);
	$_55 = NULL;
	do {
		$res_52 = $result;
		$pos_52 = $this->pos;
		$matcher = 'match_'.'minus_infinity'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_55 = TRUE; break;
		}
		$result = $res_52;
		$this->pos = $pos_52;
		$matcher = 'match_'.'integer_value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_55 = TRUE; break;
		}
		$result = $res_52;
		$this->pos = $pos_52;
		$_55 = FALSE; break;
	}
	while(0);
	if( $_55 === TRUE ) { return $this->finalise($result); }
	if( $_55 === FALSE) { return FALSE; }
}

public function integer_range_from__construct ( &$self ) {
		$self['min_value'] = "-infinity" ;
	}

public function integer_range_from_minus_infinity ( &$self, $sub ) {
		$self['min_value'] = "-infinity" ;
	}

public function integer_range_from_integer_value ( &$self, $sub ) {
		$self['min_value'] = (int)$sub['text'] ;
	}

/* integer-range-to: plus-infinity | integer-value */
protected $match_integer_range_to_typestack = array('integer_range_to');
function match_integer_range_to ($stack = array()) {
	$matchrule = "integer_range_to"; $result = $this->construct($matchrule, $matchrule, null);
	$_60 = NULL;
	do {
		$res_57 = $result;
		$pos_57 = $this->pos;
		$matcher = 'match_'.'plus_infinity'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_60 = TRUE; break;
		}
		$result = $res_57;
		$this->pos = $pos_57;
		$matcher = 'match_'.'integer_value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_60 = TRUE; break;
		}
		$result = $res_57;
		$this->pos = $pos_57;
		$_60 = FALSE; break;
	}
	while(0);
	if( $_60 === TRUE ) { return $this->finalise($result); }
	if( $_60 === FALSE) { return FALSE; }
}

public function integer_range_to__construct ( &$self ) {
		$self['max_value'] = "+infinity" ;
	}

public function integer_range_to_plus_infinity ( &$self, $sub ) {
		$self['max_value'] = "+infinity" ;
	}

public function integer_range_to_integer_value ( &$self, $sub ) {
		$self['max_value'] = (int)$sub['text'] ;
	}

/* integer-range: whitespace integer-range-from? whitespace ".." whitespace integer-range-to? whitespace */
protected $match_integer_range_typestack = array('integer_range');
function match_integer_range ($stack = array()) {
	$matchrule = "integer_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_69 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_69 = FALSE; break; }
		$res_63 = $result;
		$pos_63 = $this->pos;
		$matcher = 'match_'.'integer_range_from'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_63;
			$this->pos = $pos_63;
			unset( $res_63 );
			unset( $pos_63 );
		}
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_69 = FALSE; break; }
		if (( $subres = $this->literal( '..' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_69 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_69 = FALSE; break; }
		$res_67 = $result;
		$pos_67 = $this->pos;
		$matcher = 'match_'.'integer_range_to'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_67;
			$this->pos = $pos_67;
			unset( $res_67 );
			unset( $pos_67 );
		}
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_69 = FALSE; break; }
		$_69 = TRUE; break;
	}
	while(0);
	if( $_69 === TRUE ) { return $this->finalise($result); }
	if( $_69 === FALSE) { return FALSE; }
}

public function integer_range__construct ( &$self ) {
		$self['min_value'] = "-infinity" ;
		$self['max_value'] = "+infinity" ;
	}

public function integer_range_integer_range_from ( &$self, $sub ) {
		$self['min_value'] = $sub['min_value'] ;
	}

public function integer_range_integer_range_to ( &$self, $sub ) {
		$self['max_value'] = $sub['max_value'] ;
	}

/* real-range-from: minus-infinity | real-value | integer-value */
protected $match_real_range_from_typestack = array('real_range_from');
function match_real_range_from ($stack = array()) {
	$matchrule = "real_range_from"; $result = $this->construct($matchrule, $matchrule, null);
	$_78 = NULL;
	do {
		$res_71 = $result;
		$pos_71 = $this->pos;
		$matcher = 'match_'.'minus_infinity'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_78 = TRUE; break;
		}
		$result = $res_71;
		$this->pos = $pos_71;
		$_76 = NULL;
		do {
			$res_73 = $result;
			$pos_73 = $this->pos;
			$matcher = 'match_'.'real_value'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_76 = TRUE; break;
			}
			$result = $res_73;
			$this->pos = $pos_73;
			$matcher = 'match_'.'integer_value'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_76 = TRUE; break;
			}
			$result = $res_73;
			$this->pos = $pos_73;
			$_76 = FALSE; break;
		}
		while(0);
		if( $_76 === TRUE ) { $_78 = TRUE; break; }
		$result = $res_71;
		$this->pos = $pos_71;
		$_78 = FALSE; break;
	}
	while(0);
	if( $_78 === TRUE ) { return $this->finalise($result); }
	if( $_78 === FALSE) { return FALSE; }
}

public function real_range_from__construct ( &$self ) {
		$self['min_value'] = "-infinity" ;
	}

public function real_range_from_minus_infinity ( &$self, $sub ) {
		$self['min_value'] = "-infinity" ;
	}

public function real_range_from_real_value ( &$self, $sub ) {
		$self['min_value'] = (float)$sub['text'] ;
	}

public function real_range_from_integer_value ( &$self, $sub ) {
		$self['min_value'] = (float)$sub['text'] ;
	}

/* real-range-to: plus-infinity | real-value | integer-value */
protected $match_real_range_to_typestack = array('real_range_to');
function match_real_range_to ($stack = array()) {
	$matchrule = "real_range_to"; $result = $this->construct($matchrule, $matchrule, null);
	$_87 = NULL;
	do {
		$res_80 = $result;
		$pos_80 = $this->pos;
		$matcher = 'match_'.'plus_infinity'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_87 = TRUE; break;
		}
		$result = $res_80;
		$this->pos = $pos_80;
		$_85 = NULL;
		do {
			$res_82 = $result;
			$pos_82 = $this->pos;
			$matcher = 'match_'.'real_value'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_85 = TRUE; break;
			}
			$result = $res_82;
			$this->pos = $pos_82;
			$matcher = 'match_'.'integer_value'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_85 = TRUE; break;
			}
			$result = $res_82;
			$this->pos = $pos_82;
			$_85 = FALSE; break;
		}
		while(0);
		if( $_85 === TRUE ) { $_87 = TRUE; break; }
		$result = $res_80;
		$this->pos = $pos_80;
		$_87 = FALSE; break;
	}
	while(0);
	if( $_87 === TRUE ) { return $this->finalise($result); }
	if( $_87 === FALSE) { return FALSE; }
}

public function real_range_to__construct ( &$self ) {
		$self['max_value'] = "+infinity" ;
	}

public function real_range_to_plus_infinity ( &$self, $sub ) {
		$self['max_value'] = "+infinity" ;
	}

public function real_range_to_real_value ( &$self, $sub ) {
		$self['max_value'] = (float)$sub['text'] ;
	}

public function real_range_to_integer_value ( &$self, $sub ) {
		$self['max_value'] = (int)$sub['text'] ;
	}

/* real-range: whitespace real-range-from? whitespace ".." whitespace real-range-to? whitespace */
protected $match_real_range_typestack = array('real_range');
function match_real_range ($stack = array()) {
	$matchrule = "real_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_96 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_96 = FALSE; break; }
		$res_90 = $result;
		$pos_90 = $this->pos;
		$matcher = 'match_'.'real_range_from'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_90;
			$this->pos = $pos_90;
			unset( $res_90 );
			unset( $pos_90 );
		}
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_96 = FALSE; break; }
		if (( $subres = $this->literal( '..' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_96 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_96 = FALSE; break; }
		$res_94 = $result;
		$pos_94 = $this->pos;
		$matcher = 'match_'.'real_range_to'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_94;
			$this->pos = $pos_94;
			unset( $res_94 );
			unset( $pos_94 );
		}
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_96 = FALSE; break; }
		$_96 = TRUE; break;
	}
	while(0);
	if( $_96 === TRUE ) { return $this->finalise($result); }
	if( $_96 === FALSE) { return FALSE; }
}

public function real_range__construct ( &$self ) {
		$self['min_value'] = "-infinity" ;
		$self['max_value'] = "+infinity" ;
	}

public function real_range_real_range_from ( &$self, $sub ) {
		$self['min_value'] = $sub['min_value'] ;
	}

public function real_range_real_range_to ( &$self, $sub ) {
		$self['max_value'] = $sub['max_value'] ;
	}

/* length-range-from: non-negative-integer-value */
protected $match_length_range_from_typestack = array('length_range_from');
function match_length_range_from ($stack = array()) {
	$matchrule = "length_range_from"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'non_negative_integer_value'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres );
		return $this->finalise($result);
	}
	else { return FALSE; }
}

public function length_range_from__construct ( &$self ) {
		$self['min_length'] = "0" ;
	}

public function length_range_from_non_negative_integer_value ( &$self, $sub ) {
		$self['min_length'] = (int)$sub['text'] ;
	}

/* length-range-to: plus-infinity | non-negative-integer-value */
protected $match_length_range_to_typestack = array('length_range_to');
function match_length_range_to ($stack = array()) {
	$matchrule = "length_range_to"; $result = $this->construct($matchrule, $matchrule, null);
	$_102 = NULL;
	do {
		$res_99 = $result;
		$pos_99 = $this->pos;
		$matcher = 'match_'.'plus_infinity'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_102 = TRUE; break;
		}
		$result = $res_99;
		$this->pos = $pos_99;
		$matcher = 'match_'.'non_negative_integer_value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_102 = TRUE; break;
		}
		$result = $res_99;
		$this->pos = $pos_99;
		$_102 = FALSE; break;
	}
	while(0);
	if( $_102 === TRUE ) { return $this->finalise($result); }
	if( $_102 === FALSE) { return FALSE; }
}

public function length_range_to__construct ( &$self ) {
		$self['max_length'] = "+infinity" ;
	}

public function length_range_to_plus_infinity ( &$self, $sub ) {
		$self['max_length'] = "+infinity" ;
	}

public function length_range_to_non_negative_integer_value ( &$self, $sub ) {
		$self['max_length'] = (int)$sub['text'] ;
	}

/* length-range: whitespace length-range-from? whitespace ".." whitespace length-range-to? whitespace */
protected $match_length_range_typestack = array('length_range');
function match_length_range ($stack = array()) {
	$matchrule = "length_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_111 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_111 = FALSE; break; }
		$res_105 = $result;
		$pos_105 = $this->pos;
		$matcher = 'match_'.'length_range_from'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_105;
			$this->pos = $pos_105;
			unset( $res_105 );
			unset( $pos_105 );
		}
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_111 = FALSE; break; }
		if (( $subres = $this->literal( '..' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_111 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_111 = FALSE; break; }
		$res_109 = $result;
		$pos_109 = $this->pos;
		$matcher = 'match_'.'length_range_to'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_109;
			$this->pos = $pos_109;
			unset( $res_109 );
			unset( $pos_109 );
		}
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_111 = FALSE; break; }
		$_111 = TRUE; break;
	}
	while(0);
	if( $_111 === TRUE ) { return $this->finalise($result); }
	if( $_111 === FALSE) { return FALSE; }
}

public function length_range__construct ( &$self ) {
		$self['min_length'] = 0 ;
		$self['max_length'] = "+infinity" ;
	}

public function length_range_length_range_from ( &$self, $sub ) {
		$self['min_length'] = $sub['min_length'] ;
	}

public function length_range_length_range_to ( &$self, $sub ) {
		$self['max_length'] = $sub['max_length'] ;
	}

/* simple-type: "\b" ("Any" | "Nothing" | "Boolean" | "True" | "False" | "Null") "\b" */
protected $match_simple_type_typestack = array('simple_type');
function match_simple_type ($stack = array()) {
	$matchrule = "simple_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_138 = NULL;
	do {
		if (( $subres = $this->literal( '\b' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_138 = FALSE; break; }
		$_135 = NULL;
		do {
			$_133 = NULL;
			do {
				$res_114 = $result;
				$pos_114 = $this->pos;
				if (( $subres = $this->literal( 'Any' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_133 = TRUE; break;
				}
				$result = $res_114;
				$this->pos = $pos_114;
				$_131 = NULL;
				do {
					$res_116 = $result;
					$pos_116 = $this->pos;
					if (( $subres = $this->literal( 'Nothing' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_131 = TRUE; break;
					}
					$result = $res_116;
					$this->pos = $pos_116;
					$_129 = NULL;
					do {
						$res_118 = $result;
						$pos_118 = $this->pos;
						if (( $subres = $this->literal( 'Boolean' ) ) !== FALSE) {
							$result["text"] .= $subres;
							$_129 = TRUE; break;
						}
						$result = $res_118;
						$this->pos = $pos_118;
						$_127 = NULL;
						do {
							$res_120 = $result;
							$pos_120 = $this->pos;
							if (( $subres = $this->literal( 'True' ) ) !== FALSE) {
								$result["text"] .= $subres;
								$_127 = TRUE; break;
							}
							$result = $res_120;
							$this->pos = $pos_120;
							$_125 = NULL;
							do {
								$res_122 = $result;
								$pos_122 = $this->pos;
								if (( $subres = $this->literal( 'False' ) ) !== FALSE) {
									$result["text"] .= $subres;
									$_125 = TRUE; break;
								}
								$result = $res_122;
								$this->pos = $pos_122;
								if (( $subres = $this->literal( 'Null' ) ) !== FALSE) {
									$result["text"] .= $subres;
									$_125 = TRUE; break;
								}
								$result = $res_122;
								$this->pos = $pos_122;
								$_125 = FALSE; break;
							}
							while(0);
							if( $_125 === TRUE ) { $_127 = TRUE; break; }
							$result = $res_120;
							$this->pos = $pos_120;
							$_127 = FALSE; break;
						}
						while(0);
						if( $_127 === TRUE ) { $_129 = TRUE; break; }
						$result = $res_118;
						$this->pos = $pos_118;
						$_129 = FALSE; break;
					}
					while(0);
					if( $_129 === TRUE ) { $_131 = TRUE; break; }
					$result = $res_116;
					$this->pos = $pos_116;
					$_131 = FALSE; break;
				}
				while(0);
				if( $_131 === TRUE ) { $_133 = TRUE; break; }
				$result = $res_114;
				$this->pos = $pos_114;
				$_133 = FALSE; break;
			}
			while(0);
			if( $_133 === FALSE) { $_135 = FALSE; break; }
			$_135 = TRUE; break;
		}
		while(0);
		if( $_135 === FALSE) { $_138 = FALSE; break; }
		if (( $subres = $this->literal( '\b' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_138 = FALSE; break; }
		$_138 = TRUE; break;
	}
	while(0);
	if( $_138 === TRUE ) { return $this->finalise($result); }
	if( $_138 === FALSE) { return FALSE; }
}


/* integer-type-with-range: "Integer" "<" integer-range ">" */
protected $match_integer_type_with_range_typestack = array('integer_type_with_range');
function match_integer_type_with_range ($stack = array()) {
	$matchrule = "integer_type_with_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_144 = NULL;
	do {
		if (( $subres = $this->literal( 'Integer' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_144 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_144 = FALSE; break; }
		$matcher = 'match_'.'integer_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_144 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_144 = FALSE; break; }
		$_144 = TRUE; break;
	}
	while(0);
	if( $_144 === TRUE ) { return $this->finalise($result); }
	if( $_144 === FALSE) { return FALSE; }
}

public function integer_type_with_range_integer_range ( &$self, $sub ) {
		$self['min_value'] = $sub['min_value'] ;
		$self['max_value'] = $sub['max_value'] ;
	}

/* integer-type-without-range: "\bInteger\b" */
protected $match_integer_type_without_range_typestack = array('integer_type_without_range');
function match_integer_type_without_range ($stack = array()) {
	$matchrule = "integer_type_without_range"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '\bInteger\b' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* integer-type: integer-type-with-range | integer-type-without-range */
protected $match_integer_type_typestack = array('integer_type');
function match_integer_type ($stack = array()) {
	$matchrule = "integer_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_150 = NULL;
	do {
		$res_147 = $result;
		$pos_147 = $this->pos;
		$matcher = 'match_'.'integer_type_with_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_150 = TRUE; break;
		}
		$result = $res_147;
		$this->pos = $pos_147;
		$matcher = 'match_'.'integer_type_without_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_150 = TRUE; break;
		}
		$result = $res_147;
		$this->pos = $pos_147;
		$_150 = FALSE; break;
	}
	while(0);
	if( $_150 === TRUE ) { return $this->finalise($result); }
	if( $_150 === FALSE) { return FALSE; }
}

public function integer_type__construct ( &$self ) {
		$self['range'] = [];
		$self['range']['node'] = "range" ;
		$self['range']['type'] = "integer_range" ;
	}

public function integer_type_integer_type_with_range ( &$self, $sub ) {
		$self['range']['min_value'] = $sub['min_value'] ;
		$self['range']['max_value'] = $sub['max_value'] ;
	}

public function integer_type_integer_type_without_range ( &$self, $sub ) {
		$self['range']['min_value'] = "-infinity" ;
		$self['range']['max_value'] = "+infinity" ;
	}

/* real-type-with-range: "Real" "<" real-range ">" */
protected $match_real_type_with_range_typestack = array('real_type_with_range');
function match_real_type_with_range ($stack = array()) {
	$matchrule = "real_type_with_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_156 = NULL;
	do {
		if (( $subres = $this->literal( 'Real' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_156 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_156 = FALSE; break; }
		$matcher = 'match_'.'real_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_156 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_156 = FALSE; break; }
		$_156 = TRUE; break;
	}
	while(0);
	if( $_156 === TRUE ) { return $this->finalise($result); }
	if( $_156 === FALSE) { return FALSE; }
}

public function real_type_with_range_real_range ( &$self, $sub ) {
		$self['min_value'] = $sub['min_value'] ;
		$self['max_value'] = $sub['max_value'] ;
	}

/* real-type-without-range: "\bReal\b" */
protected $match_real_type_without_range_typestack = array('real_type_without_range');
function match_real_type_without_range ($stack = array()) {
	$matchrule = "real_type_without_range"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '\bReal\b' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* real-type: real-type-with-range | real-type-without-range */
protected $match_real_type_typestack = array('real_type');
function match_real_type ($stack = array()) {
	$matchrule = "real_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_162 = NULL;
	do {
		$res_159 = $result;
		$pos_159 = $this->pos;
		$matcher = 'match_'.'real_type_with_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_162 = TRUE; break;
		}
		$result = $res_159;
		$this->pos = $pos_159;
		$matcher = 'match_'.'real_type_without_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_162 = TRUE; break;
		}
		$result = $res_159;
		$this->pos = $pos_159;
		$_162 = FALSE; break;
	}
	while(0);
	if( $_162 === TRUE ) { return $this->finalise($result); }
	if( $_162 === FALSE) { return FALSE; }
}

public function real_type__construct ( &$self ) {
		$self['range'] = [];
		$self['range']['node'] = "range" ;
		$self['range']['type'] = "real_range" ;
	}

public function real_type_real_type_with_range ( &$self, $sub ) {
		$self['range']['min_value'] = $sub['min_value'] ;
		$self['range']['max_value'] = $sub['max_value'] ;
	}

public function real_type_real_type_without_range ( &$self, $sub ) {
		$self['range']['min_value'] = "-infinity" ;
		$self['range']['max_value'] = "+infinity" ;
	}

/* string-type-with-range: "String" "<" length-range ">" */
protected $match_string_type_with_range_typestack = array('string_type_with_range');
function match_string_type_with_range ($stack = array()) {
	$matchrule = "string_type_with_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_168 = NULL;
	do {
		if (( $subres = $this->literal( 'String' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_168 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_168 = FALSE; break; }
		$matcher = 'match_'.'length_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_168 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_168 = FALSE; break; }
		$_168 = TRUE; break;
	}
	while(0);
	if( $_168 === TRUE ) { return $this->finalise($result); }
	if( $_168 === FALSE) { return FALSE; }
}

public function string_type_with_range_length_range ( &$self, $sub ) {
		$self['min_length'] = $sub['min_length'] ;
		$self['max_length'] = $sub['max_length'] ;
	}

/* string-type-without-range: "\bString\b" */
protected $match_string_type_without_range_typestack = array('string_type_without_range');
function match_string_type_without_range ($stack = array()) {
	$matchrule = "string_type_without_range"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '\bString\b' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* string-type: string-type-with-range | string-type-without-range */
protected $match_string_type_typestack = array('string_type');
function match_string_type ($stack = array()) {
	$matchrule = "string_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_174 = NULL;
	do {
		$res_171 = $result;
		$pos_171 = $this->pos;
		$matcher = 'match_'.'string_type_with_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_174 = TRUE; break;
		}
		$result = $res_171;
		$this->pos = $pos_171;
		$matcher = 'match_'.'string_type_without_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_174 = TRUE; break;
		}
		$result = $res_171;
		$this->pos = $pos_171;
		$_174 = FALSE; break;
	}
	while(0);
	if( $_174 === TRUE ) { return $this->finalise($result); }
	if( $_174 === FALSE) { return FALSE; }
}

public function string_type__construct ( &$self ) {
		$self['length_range'] = [];
		$self['length_range']['node'] = "range" ;
		$self['length_range']['type'] = "length_range" ;
	}

public function string_type_string_type_with_range ( &$self, $sub ) {
		$self['length_range']['min_length'] = $sub['min_length'] ;
		$self['length_range']['max_length'] = $sub['max_length'] ;
	}

public function string_type_string_type_without_range ( &$self, $sub ) {
		$self['length_range']['min_length'] = "0" ;
		$self['length_range']['max_length'] = "+infinity" ;
	}

/* array-type-with-item-type-and-range: "Array" "<" whitespace type-expression whitespace "," length-range ">" */
protected $match_array_type_with_item_type_and_range_typestack = array('array_type_with_item_type_and_range');
function match_array_type_with_item_type_and_range ($stack = array()) {
	$matchrule = "array_type_with_item_type_and_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_184 = NULL;
	do {
		if (( $subres = $this->literal( 'Array' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_184 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_184 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_184 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_184 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_184 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ',') {
			$this->pos += 1;
			$result["text"] .= ',';
		}
		else { $_184 = FALSE; break; }
		$matcher = 'match_'.'length_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_184 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_184 = FALSE; break; }
		$_184 = TRUE; break;
	}
	while(0);
	if( $_184 === TRUE ) { return $this->finalise($result); }
	if( $_184 === FALSE) { return FALSE; }
}

public function array_type_with_item_type_and_range_type_expression ( &$self, $sub ) {
		$self['item_type'] = $sub ;
	}

public function array_type_with_item_type_and_range_length_range ( &$self, $sub ) {
		$self['min_length'] = $sub['min_length'] ;
		$self['max_length'] = $sub['max_length'] ;
	}

/* array-type-with-range: "Array" "<" length-range ">" */
protected $match_array_type_with_range_typestack = array('array_type_with_range');
function match_array_type_with_range ($stack = array()) {
	$matchrule = "array_type_with_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_190 = NULL;
	do {
		if (( $subres = $this->literal( 'Array' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_190 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_190 = FALSE; break; }
		$matcher = 'match_'.'length_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_190 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_190 = FALSE; break; }
		$_190 = TRUE; break;
	}
	while(0);
	if( $_190 === TRUE ) { return $this->finalise($result); }
	if( $_190 === FALSE) { return FALSE; }
}

public function array_type_with_range_length_range ( &$self, $sub ) {
		$self['min_length'] = $sub['min_length'] ;
		$self['max_length'] = $sub['max_length'] ;
	}

/* array-type-with-item-type: "Array" "<" whitespace type-expression whitespace ">" */
protected $match_array_type_with_item_type_typestack = array('array_type_with_item_type');
function match_array_type_with_item_type ($stack = array()) {
	$matchrule = "array_type_with_item_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_198 = NULL;
	do {
		if (( $subres = $this->literal( 'Array' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_198 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_198 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_198 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_198 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_198 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_198 = FALSE; break; }
		$_198 = TRUE; break;
	}
	while(0);
	if( $_198 === TRUE ) { return $this->finalise($result); }
	if( $_198 === FALSE) { return FALSE; }
}

public function array_type_with_item_type_type_expression ( &$self, $sub ) {
		$self['item_type'] = $sub ;
	}

/* array-type-without-item-type-and-range: "\bArray\b" */
protected $match_array_type_without_item_type_and_range_typestack = array('array_type_without_item_type_and_range');
function match_array_type_without_item_type_and_range ($stack = array()) {
	$matchrule = "array_type_without_item_type_and_range"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '\bArray\b' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* array-type: array-type-with-item-type-and-range | array-type-with-range | array-type-with-item-type | array-type-without-item-type-and-range */
protected $match_array_type_typestack = array('array_type');
function match_array_type ($stack = array()) {
	$matchrule = "array_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_212 = NULL;
	do {
		$res_201 = $result;
		$pos_201 = $this->pos;
		$matcher = 'match_'.'array_type_with_item_type_and_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_212 = TRUE; break;
		}
		$result = $res_201;
		$this->pos = $pos_201;
		$_210 = NULL;
		do {
			$res_203 = $result;
			$pos_203 = $this->pos;
			$matcher = 'match_'.'array_type_with_range'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_210 = TRUE; break;
			}
			$result = $res_203;
			$this->pos = $pos_203;
			$_208 = NULL;
			do {
				$res_205 = $result;
				$pos_205 = $this->pos;
				$matcher = 'match_'.'array_type_with_item_type'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_208 = TRUE; break;
				}
				$result = $res_205;
				$this->pos = $pos_205;
				$matcher = 'match_'.'array_type_without_item_type_and_range'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_208 = TRUE; break;
				}
				$result = $res_205;
				$this->pos = $pos_205;
				$_208 = FALSE; break;
			}
			while(0);
			if( $_208 === TRUE ) { $_210 = TRUE; break; }
			$result = $res_203;
			$this->pos = $pos_203;
			$_210 = FALSE; break;
		}
		while(0);
		if( $_210 === TRUE ) { $_212 = TRUE; break; }
		$result = $res_201;
		$this->pos = $pos_201;
		$_212 = FALSE; break;
	}
	while(0);
	if( $_212 === TRUE ) { return $this->finalise($result); }
	if( $_212 === FALSE) { return FALSE; }
}

public function array_type__construct ( &$self ) {
		$self['item_type'] = [];
		$self['item_type']['node'] = 'type_term';
		$self['item_type']['type'] = 'any';
		$self['length_range'] = [];
		$self['length_range']['node'] = "range" ;
		$self['length_range']['type'] = "length_range" ;
		$self['length_range']['min_length'] = "0" ;
		$self['length_range']['max_length'] = "+infinity" ;
	}

public function array_type_array_type_with_item_type_and_range ( &$self, $sub ) {
		$self['item_type'] = $sub['item_type'];
		$self['length_range']['min_length'] = $sub['min_length'] ;
		$self['length_range']['max_length'] = $sub['max_length'] ;
	}

public function array_type_array_type_with_item_type ( &$self, $sub ) {
		$self['item_type'] = $sub['item_type'];
	}

public function array_type_array_type_with_range ( &$self, $sub ) {
		$self['length_range']['min_length'] = $sub['min_length'] ;
		$self['length_range']['max_length'] = $sub['max_length'] ;
	}

public function array_type_array_type_without_item_type_and_range ( &$self, $sub ) {
	}

/* map-type-with-item-type-and-range: "Map" "<" whitespace type-expression whitespace "," length-range ">" */
protected $match_map_type_with_item_type_and_range_typestack = array('map_type_with_item_type_and_range');
function match_map_type_with_item_type_and_range ($stack = array()) {
	$matchrule = "map_type_with_item_type_and_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_222 = NULL;
	do {
		if (( $subres = $this->literal( 'Map' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_222 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_222 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_222 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_222 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_222 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ',') {
			$this->pos += 1;
			$result["text"] .= ',';
		}
		else { $_222 = FALSE; break; }
		$matcher = 'match_'.'length_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_222 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_222 = FALSE; break; }
		$_222 = TRUE; break;
	}
	while(0);
	if( $_222 === TRUE ) { return $this->finalise($result); }
	if( $_222 === FALSE) { return FALSE; }
}

public function map_type_with_item_type_and_range_type_expression ( &$self, $sub ) {
		$self['item_type'] = $sub ;
	}

public function map_type_with_item_type_and_range_length_range ( &$self, $sub ) {
		$self['min_length'] = $sub['min_length'] ;
		$self['max_length'] = $sub['max_length'] ;
	}

/* map-type-with-range: "Map" "<" length-range ">" */
protected $match_map_type_with_range_typestack = array('map_type_with_range');
function match_map_type_with_range ($stack = array()) {
	$matchrule = "map_type_with_range"; $result = $this->construct($matchrule, $matchrule, null);
	$_228 = NULL;
	do {
		if (( $subres = $this->literal( 'Map' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_228 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_228 = FALSE; break; }
		$matcher = 'match_'.'length_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_228 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_228 = FALSE; break; }
		$_228 = TRUE; break;
	}
	while(0);
	if( $_228 === TRUE ) { return $this->finalise($result); }
	if( $_228 === FALSE) { return FALSE; }
}

public function map_type_with_range_length_range ( &$self, $sub ) {
		$self['min_length'] = $sub['min_length'] ;
		$self['max_length'] = $sub['max_length'] ;
	}

/* map-type-with-item-type: "Map" "<" whitespace type-expression whitespace ">" */
protected $match_map_type_with_item_type_typestack = array('map_type_with_item_type');
function match_map_type_with_item_type ($stack = array()) {
	$matchrule = "map_type_with_item_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_236 = NULL;
	do {
		if (( $subres = $this->literal( 'Map' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_236 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_236 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_236 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_236 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_236 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_236 = FALSE; break; }
		$_236 = TRUE; break;
	}
	while(0);
	if( $_236 === TRUE ) { return $this->finalise($result); }
	if( $_236 === FALSE) { return FALSE; }
}

public function map_type_with_item_type_type_expression ( &$self, $sub ) {
		$self['item_type'] = $sub ;
	}

/* map-type-without-item-type-and-range: "\bMap\b" */
protected $match_map_type_without_item_type_and_range_typestack = array('map_type_without_item_type_and_range');
function match_map_type_without_item_type_and_range ($stack = array()) {
	$matchrule = "map_type_without_item_type_and_range"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '\bMap\b' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* map-type: map-type-with-item-type-and-range | map-type-with-range | map-type-with-item-type | map-type-without-item-type-and-range */
protected $match_map_type_typestack = array('map_type');
function match_map_type ($stack = array()) {
	$matchrule = "map_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_250 = NULL;
	do {
		$res_239 = $result;
		$pos_239 = $this->pos;
		$matcher = 'match_'.'map_type_with_item_type_and_range'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_250 = TRUE; break;
		}
		$result = $res_239;
		$this->pos = $pos_239;
		$_248 = NULL;
		do {
			$res_241 = $result;
			$pos_241 = $this->pos;
			$matcher = 'match_'.'map_type_with_range'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_248 = TRUE; break;
			}
			$result = $res_241;
			$this->pos = $pos_241;
			$_246 = NULL;
			do {
				$res_243 = $result;
				$pos_243 = $this->pos;
				$matcher = 'match_'.'map_type_with_item_type'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_246 = TRUE; break;
				}
				$result = $res_243;
				$this->pos = $pos_243;
				$matcher = 'match_'.'map_type_without_item_type_and_range'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_246 = TRUE; break;
				}
				$result = $res_243;
				$this->pos = $pos_243;
				$_246 = FALSE; break;
			}
			while(0);
			if( $_246 === TRUE ) { $_248 = TRUE; break; }
			$result = $res_241;
			$this->pos = $pos_241;
			$_248 = FALSE; break;
		}
		while(0);
		if( $_248 === TRUE ) { $_250 = TRUE; break; }
		$result = $res_239;
		$this->pos = $pos_239;
		$_250 = FALSE; break;
	}
	while(0);
	if( $_250 === TRUE ) { return $this->finalise($result); }
	if( $_250 === FALSE) { return FALSE; }
}

public function map_type__construct ( &$self ) {
		$self['item_type'] = [];
		$self['item_type']['node'] = 'type_term';
		$self['item_type']['type'] = 'any';
		$self['length_range'] = [];
		$self['length_range']['node'] = "range" ;
		$self['length_range']['type'] = "length_range" ;
		$self['length_range']['min_length'] = "0" ;
		$self['length_range']['max_length'] = "+infinity" ;
	}

public function map_type_map_type_with_item_type_and_range ( &$self, $sub ) {
		$self['item_type'] = $sub['item_type'];
		$self['length_range']['min_length'] = $sub['min_length'] ;
		$self['length_range']['max_length'] = $sub['max_length'] ;
	}

public function map_type_map_type_with_item_type ( &$self, $sub ) {
		$self['item_type'] = $sub['item_type'];
	}

public function map_type_map_type_with_range ( &$self, $sub ) {
		$self['length_range']['min_length'] = $sub['min_length'] ;
		$self['length_range']['max_length'] = $sub['max_length'] ;
	}

public function map_type_map_type_without_item_type_and_range ( &$self, $sub ) {
	}

/* type-type-with-ref-type: "Type" "<" whitespace type-expression whitespace ">" */
protected $match_type_type_with_ref_type_typestack = array('type_type_with_ref_type');
function match_type_type_with_ref_type ($stack = array()) {
	$matchrule = "type_type_with_ref_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_258 = NULL;
	do {
		if (( $subres = $this->literal( 'Type' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_258 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_258 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_258 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_258 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_258 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_258 = FALSE; break; }
		$_258 = TRUE; break;
	}
	while(0);
	if( $_258 === TRUE ) { return $this->finalise($result); }
	if( $_258 === FALSE) { return FALSE; }
}

public function type_type_with_ref_type_type_expression ( &$self, $sub ) {
		$self['item_type'] = $sub ;
	}

/* type-type-without-ref-type: "\bType\b" */
protected $match_type_type_without_ref_type_typestack = array('type_type_without_ref_type');
function match_type_type_without_ref_type ($stack = array()) {
	$matchrule = "type_type_without_ref_type"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '\bType\b' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* type-type: type-type-with-ref-type | type-type-without-ref-type */
protected $match_type_type_typestack = array('type_type');
function match_type_type ($stack = array()) {
	$matchrule = "type_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_264 = NULL;
	do {
		$res_261 = $result;
		$pos_261 = $this->pos;
		$matcher = 'match_'.'type_type_with_ref_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_264 = TRUE; break;
		}
		$result = $res_261;
		$this->pos = $pos_261;
		$matcher = 'match_'.'type_type_without_ref_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_264 = TRUE; break;
		}
		$result = $res_261;
		$this->pos = $pos_261;
		$_264 = FALSE; break;
	}
	while(0);
	if( $_264 === TRUE ) { return $this->finalise($result); }
	if( $_264 === FALSE) { return FALSE; }
}

public function type_type__construct ( &$self ) {
		$self['ref_type'] = [];
		$self['ref_type']['node'] = 'type_term';
		$self['ref_type']['type'] = 'any';
	}

public function type_type_type_type_with_ref_type ( &$self, $sub ) {
		$self['ref_type'] = $sub['item_type'];
	}

/* mutable-type-with-ref-type: "Mutable" "<" whitespace type-expression whitespace ">" */
protected $match_mutable_type_with_ref_type_typestack = array('mutable_type_with_ref_type');
function match_mutable_type_with_ref_type ($stack = array()) {
	$matchrule = "mutable_type_with_ref_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_272 = NULL;
	do {
		if (( $subres = $this->literal( 'Mutable' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_272 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_272 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_272 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_272 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_272 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_272 = FALSE; break; }
		$_272 = TRUE; break;
	}
	while(0);
	if( $_272 === TRUE ) { return $this->finalise($result); }
	if( $_272 === FALSE) { return FALSE; }
}

public function mutable_type_with_ref_type_type_expression ( &$self, $sub ) {
		$self['item_type'] = $sub ;
	}

/* mutable-type-without-ref-type: "\bMutable\b" */
protected $match_mutable_type_without_ref_type_typestack = array('mutable_type_without_ref_type');
function match_mutable_type_without_ref_type ($stack = array()) {
	$matchrule = "mutable_type_without_ref_type"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '\bMutable\b' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* mutable-type: mutable-type-with-ref-type | mutable-type-without-ref-type */
protected $match_mutable_type_typestack = array('mutable_type');
function match_mutable_type ($stack = array()) {
	$matchrule = "mutable_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_278 = NULL;
	do {
		$res_275 = $result;
		$pos_275 = $this->pos;
		$matcher = 'match_'.'mutable_type_with_ref_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_278 = TRUE; break;
		}
		$result = $res_275;
		$this->pos = $pos_275;
		$matcher = 'match_'.'mutable_type_without_ref_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_278 = TRUE; break;
		}
		$result = $res_275;
		$this->pos = $pos_275;
		$_278 = FALSE; break;
	}
	while(0);
	if( $_278 === TRUE ) { return $this->finalise($result); }
	if( $_278 === FALSE) { return FALSE; }
}

public function mutable_type__construct ( &$self ) {
		$self['ref_type'] = [];
		$self['ref_type']['node'] = 'type_term';
		$self['ref_type']['type'] = 'any';
	}

public function mutable_type_mutable_type_with_ref_type ( &$self, $sub ) {
		$self['ref_type'] = $sub['item_type'];
	}

/* any-error: "@@" */
protected $match_any_error_typestack = array('any_error');
function match_any_error ($stack = array()) {
	$matchrule = "any_error"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '@@' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* explicit-error: "@" */
protected $match_explicit_error_typestack = array('explicit_error');
function match_explicit_error ($stack = array()) {
	$matchrule = "explicit_error"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '@') {
		$this->pos += 1;
		$result["text"] .= '@';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* any-or-specific-error: "@@" whitespace type-expression */
protected $match_any_or_specific_error_typestack = array('any_or_specific_error');
function match_any_or_specific_error ($stack = array()) {
	$matchrule = "any_or_specific_error"; $result = $this->construct($matchrule, $matchrule, null);
	$_285 = NULL;
	do {
		if (( $subres = $this->literal( '@@' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_285 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_285 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_285 = FALSE; break; }
		$_285 = TRUE; break;
	}
	while(0);
	if( $_285 === TRUE ) { return $this->finalise($result); }
	if( $_285 === FALSE) { return FALSE; }
}

public function any_or_specific_error_type_expression ( &$self, $sub ) {
		$self['error_type'] = $sub;
	}

/* specific-error: "@" whitespace type-expression */
protected $match_specific_error_typestack = array('specific_error');
function match_specific_error ($stack = array()) {
	$matchrule = "specific_error"; $result = $this->construct($matchrule, $matchrule, null);
	$_290 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '@') {
			$this->pos += 1;
			$result["text"] .= '@';
		}
		else { $_290 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_290 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_290 = FALSE; break; }
		$_290 = TRUE; break;
	}
	while(0);
	if( $_290 === TRUE ) { return $this->finalise($result); }
	if( $_290 === FALSE) { return FALSE; }
}

public function specific_error_type_expression ( &$self, $sub ) {
		$self['error_type'] = $sub;
	}

/* error-type: any-or-specific-error | specific-error | any-error */
protected $match_error_type_typestack = array('error_type');
function match_error_type ($stack = array()) {
	$matchrule = "error_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_299 = NULL;
	do {
		$res_292 = $result;
		$pos_292 = $this->pos;
		$matcher = 'match_'.'any_or_specific_error'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_299 = TRUE; break;
		}
		$result = $res_292;
		$this->pos = $pos_292;
		$_297 = NULL;
		do {
			$res_294 = $result;
			$pos_294 = $this->pos;
			$matcher = 'match_'.'specific_error'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_297 = TRUE; break;
			}
			$result = $res_294;
			$this->pos = $pos_294;
			$matcher = 'match_'.'any_error'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_297 = TRUE; break;
			}
			$result = $res_294;
			$this->pos = $pos_294;
			$_297 = FALSE; break;
		}
		while(0);
		if( $_297 === TRUE ) { $_299 = TRUE; break; }
		$result = $res_292;
		$this->pos = $pos_292;
		$_299 = FALSE; break;
	}
	while(0);
	if( $_299 === TRUE ) { return $this->finalise($result); }
	if( $_299 === FALSE) { return FALSE; }
}

public function error_type_any_error ( &$self, $sub ) {
		$self['any_error'] = true;
		$self['error_type'] = [
			'node' => 'type_term',
			'type' => 'nothing'
		];
	}

public function error_type_any_or_specific_error ( &$self, $sub ) {
		$self['any_error'] = true;
		$self['error_type'] = $sub['error_type'];
	}

public function error_type_specific_error ( &$self, $sub ) {
		$self['any_error'] = false;
		$self['error_type'] = $sub['error_type'];
	}

/* return-type: type-expression whitespace error-type? */
protected $match_return_type_typestack = array('return_type');
function match_return_type ($stack = array()) {
	$matchrule = "return_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_304 = NULL;
	do {
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_304 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_304 = FALSE; break; }
		$res_303 = $result;
		$pos_303 = $this->pos;
		$matcher = 'match_'.'error_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_303;
			$this->pos = $pos_303;
			unset( $res_303 );
			unset( $pos_303 );
		}
		$_304 = TRUE; break;
	}
	while(0);
	if( $_304 === TRUE ) { return $this->finalise($result); }
	if( $_304 === FALSE) { return FALSE; }
}

public function return_type__construct ( &$self ) {
		$self['node'] = 'function_return_type_term';
		$self['any_error'] = false;
		$self['error_type'] = [
			'node' => 'type_term',
			'type' => 'nothing'
		];
	}

public function return_type_type_expression ( &$self, $sub ) {
		$self['return_type'] = $sub;
	}

public function return_type_error_type ( &$self, $sub ) {
		$self['any_error'] = $sub['any_error'];
		$self['error_type'] = $sub['error_type'];
	}

/* function-type: '^' type-expression whitespace "=>" whitespace return-type */
protected $match_function_type_typestack = array('function_type');
function match_function_type ($stack = array()) {
	$matchrule = "function_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_312 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '^') {
			$this->pos += 1;
			$result["text"] .= '^';
		}
		else { $_312 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_312 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_312 = FALSE; break; }
		if (( $subres = $this->literal( '=>' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_312 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_312 = FALSE; break; }
		$matcher = 'match_'.'return_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_312 = FALSE; break; }
		$_312 = TRUE; break;
	}
	while(0);
	if( $_312 === TRUE ) { return $this->finalise($result); }
	if( $_312 === FALSE) { return FALSE; }
}

public function function_type_type_expression ( &$self, $sub ) {
		$self['parameter_type'] = $sub ;
	}

public function function_type_return_type ( &$self, $sub ) {
		$self['return_type'] = $sub ;
	}

/* non-empty-tuple-type: "[" whitespace (type-expression whitespace "," whitespace)* type-expression whitespace "]" */
protected $match_non_empty_tuple_type_typestack = array('non_empty_tuple_type');
function match_non_empty_tuple_type ($stack = array()) {
	$matchrule = "non_empty_tuple_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_325 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_325 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_325 = FALSE; break; }
		while (true) {
			$res_321 = $result;
			$pos_321 = $this->pos;
			$_320 = NULL;
			do {
				$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_320 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_320 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_320 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_320 = FALSE; break; }
				$_320 = TRUE; break;
			}
			while(0);
			if( $_320 === FALSE) {
				$result = $res_321;
				$this->pos = $pos_321;
				unset( $res_321 );
				unset( $pos_321 );
				break;
			}
		}
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_325 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_325 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_325 = FALSE; break; }
		$_325 = TRUE; break;
	}
	while(0);
	if( $_325 === TRUE ) { return $this->finalise($result); }
	if( $_325 === FALSE) { return FALSE; }
}

public function non_empty_tuple_type__construct ( &$self ) {
		$self['types'] = array();
	}

public function non_empty_tuple_type_type_expression ( &$self, $sub ) {
		$self['types'][] = $sub;
	}

/* tuple-type: "[]" | non-empty-tuple-type */
protected $match_tuple_type_typestack = array('tuple_type');
function match_tuple_type ($stack = array()) {
	$matchrule = "tuple_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_330 = NULL;
	do {
		$res_327 = $result;
		$pos_327 = $this->pos;
		if (( $subres = $this->literal( '[]' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_330 = TRUE; break;
		}
		$result = $res_327;
		$this->pos = $pos_327;
		$matcher = 'match_'.'non_empty_tuple_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_330 = TRUE; break;
		}
		$result = $res_327;
		$this->pos = $pos_327;
		$_330 = FALSE; break;
	}
	while(0);
	if( $_330 === TRUE ) { return $this->finalise($result); }
	if( $_330 === FALSE) { return FALSE; }
}

public function tuple_type__construct ( &$self ) {
		$self['types'] = array();
	}

public function tuple_type_non_empty_tuple_type ( &$self, $sub ) {
		$self['types'] = $sub['types'];
	}

/* record-key-type-pair: (record-key whitespace ":" whitespace type-expression) | ("~" type-expression) */
protected $match_record_key_type_pair_typestack = array('record_key_type_pair');
function match_record_key_type_pair ($stack = array()) {
	$matchrule = "record_key_type_pair"; $result = $this->construct($matchrule, $matchrule, null);
	$_344 = NULL;
	do {
		$res_332 = $result;
		$pos_332 = $this->pos;
		$_338 = NULL;
		do {
			$matcher = 'match_'.'record_key'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_338 = FALSE; break; }
			$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_338 = FALSE; break; }
			if (substr($this->string,$this->pos,1) == ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_338 = FALSE; break; }
			$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_338 = FALSE; break; }
			$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_338 = FALSE; break; }
			$_338 = TRUE; break;
		}
		while(0);
		if( $_338 === TRUE ) { $_344 = TRUE; break; }
		$result = $res_332;
		$this->pos = $pos_332;
		$_342 = NULL;
		do {
			if (substr($this->string,$this->pos,1) == '~') {
				$this->pos += 1;
				$result["text"] .= '~';
			}
			else { $_342 = FALSE; break; }
			$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_342 = FALSE; break; }
			$_342 = TRUE; break;
		}
		while(0);
		if( $_342 === TRUE ) { $_344 = TRUE; break; }
		$result = $res_332;
		$this->pos = $pos_332;
		$_344 = FALSE; break;
	}
	while(0);
	if( $_344 === TRUE ) { return $this->finalise($result); }
	if( $_344 === FALSE) { return FALSE; }
}

public function record_key_type_pair_record_key ( &$self, $sub ) {
		$self['key'] = $sub['text'];
	}

public function record_key_type_pair_type_expression ( &$self, $sub ) {
		$self['type'] = $sub;
	}

/* non-empty-record-type: "[" whitespace (record-key-type-pair whitespace "," whitespace)* record-key-type-pair whitespace "]" */
protected $match_non_empty_record_type_typestack = array('non_empty_record_type');
function match_non_empty_record_type ($stack = array()) {
	$matchrule = "non_empty_record_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_357 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_357 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_357 = FALSE; break; }
		while (true) {
			$res_353 = $result;
			$pos_353 = $this->pos;
			$_352 = NULL;
			do {
				$matcher = 'match_'.'record_key_type_pair'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_352 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_352 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_352 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_352 = FALSE; break; }
				$_352 = TRUE; break;
			}
			while(0);
			if( $_352 === FALSE) {
				$result = $res_353;
				$this->pos = $pos_353;
				unset( $res_353 );
				unset( $pos_353 );
				break;
			}
		}
		$matcher = 'match_'.'record_key_type_pair'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_357 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_357 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_357 = FALSE; break; }
		$_357 = TRUE; break;
	}
	while(0);
	if( $_357 === TRUE ) { return $this->finalise($result); }
	if( $_357 === FALSE) { return FALSE; }
}

public function non_empty_record_type__construct ( &$self ) {
		$self['types'] = array();
	}

public function non_empty_record_type_record_key_type_pair ( &$self, $sub ) {
		$key = $sub['key'] ?? strtolower($sub['type']['type_name'][0]) . substr($sub['type']['type_name'], 1);
		$self['types'][$key] = $sub['type'];
	}

/* record-type: "[:]" | non-empty-record-type */
protected $match_record_type_typestack = array('record_type');
function match_record_type ($stack = array()) {
	$matchrule = "record_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_362 = NULL;
	do {
		$res_359 = $result;
		$pos_359 = $this->pos;
		if (( $subres = $this->literal( '[:]' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_362 = TRUE; break;
		}
		$result = $res_359;
		$this->pos = $pos_359;
		$matcher = 'match_'.'non_empty_record_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_362 = TRUE; break;
		}
		$result = $res_359;
		$this->pos = $pos_359;
		$_362 = FALSE; break;
	}
	while(0);
	if( $_362 === TRUE ) { return $this->finalise($result); }
	if( $_362 === FALSE) { return FALSE; }
}

public function record_type__construct ( &$self ) {
		$self['types'] = array();
	}

public function record_type_non_empty_record_type ( &$self, $sub ) {
		$self['types'] = $sub['types'];
	}

/* union-type: "(" whitespace (type-expression whitespace "|" whitespace)* type-expression whitespace ")" */
protected $match_union_type_typestack = array('union_type');
function match_union_type ($stack = array()) {
	$matchrule = "union_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_375 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_375 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_375 = FALSE; break; }
		while (true) {
			$res_371 = $result;
			$pos_371 = $this->pos;
			$_370 = NULL;
			do {
				$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_370 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_370 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == '|') {
					$this->pos += 1;
					$result["text"] .= '|';
				}
				else { $_370 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_370 = FALSE; break; }
				$_370 = TRUE; break;
			}
			while(0);
			if( $_370 === FALSE) {
				$result = $res_371;
				$this->pos = $pos_371;
				unset( $res_371 );
				unset( $pos_371 );
				break;
			}
		}
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_375 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_375 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_375 = FALSE; break; }
		$_375 = TRUE; break;
	}
	while(0);
	if( $_375 === TRUE ) { return $this->finalise($result); }
	if( $_375 === FALSE) { return FALSE; }
}

public function union_type__construct ( &$self ) {
		$self['types'] = array();
	}

public function union_type_type_expression ( &$self, $sub ) {
		$self['types'][] = $sub;
	}

/* intersection-type: "(" whitespace (type-expression whitespace "&" whitespace)* type-expression whitespace ")" */
protected $match_intersection_type_typestack = array('intersection_type');
function match_intersection_type ($stack = array()) {
	$matchrule = "intersection_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_388 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_388 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_388 = FALSE; break; }
		while (true) {
			$res_384 = $result;
			$pos_384 = $this->pos;
			$_383 = NULL;
			do {
				$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_383 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_383 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == '&') {
					$this->pos += 1;
					$result["text"] .= '&';
				}
				else { $_383 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_383 = FALSE; break; }
				$_383 = TRUE; break;
			}
			while(0);
			if( $_383 === FALSE) {
				$result = $res_384;
				$this->pos = $pos_384;
				unset( $res_384 );
				unset( $pos_384 );
				break;
			}
		}
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_388 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_388 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_388 = FALSE; break; }
		$_388 = TRUE; break;
	}
	while(0);
	if( $_388 === TRUE ) { return $this->finalise($result); }
	if( $_388 === FALSE) { return FALSE; }
}

public function intersection_type__construct ( &$self ) {
		$self['types'] = array();
	}

public function intersection_type_type_expression ( &$self, $sub ) {
		$self['types'][] = $sub;
	}

/* type-expression: record-type | tuple-type | union-type | intersection-type | function-type | type-literal */
protected $match_type_expression_typestack = array('type_expression');
function match_type_expression ($stack = array()) {
	$matchrule = "type_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_409 = NULL;
	do {
		$res_390 = $result;
		$pos_390 = $this->pos;
		$matcher = 'match_'.'record_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_409 = TRUE; break;
		}
		$result = $res_390;
		$this->pos = $pos_390;
		$_407 = NULL;
		do {
			$res_392 = $result;
			$pos_392 = $this->pos;
			$matcher = 'match_'.'tuple_type'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_407 = TRUE; break;
			}
			$result = $res_392;
			$this->pos = $pos_392;
			$_405 = NULL;
			do {
				$res_394 = $result;
				$pos_394 = $this->pos;
				$matcher = 'match_'.'union_type'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_405 = TRUE; break;
				}
				$result = $res_394;
				$this->pos = $pos_394;
				$_403 = NULL;
				do {
					$res_396 = $result;
					$pos_396 = $this->pos;
					$matcher = 'match_'.'intersection_type'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_403 = TRUE; break;
					}
					$result = $res_396;
					$this->pos = $pos_396;
					$_401 = NULL;
					do {
						$res_398 = $result;
						$pos_398 = $this->pos;
						$matcher = 'match_'.'function_type'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_401 = TRUE; break;
						}
						$result = $res_398;
						$this->pos = $pos_398;
						$matcher = 'match_'.'type_literal'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_401 = TRUE; break;
						}
						$result = $res_398;
						$this->pos = $pos_398;
						$_401 = FALSE; break;
					}
					while(0);
					if( $_401 === TRUE ) { $_403 = TRUE; break; }
					$result = $res_396;
					$this->pos = $pos_396;
					$_403 = FALSE; break;
				}
				while(0);
				if( $_403 === TRUE ) { $_405 = TRUE; break; }
				$result = $res_394;
				$this->pos = $pos_394;
				$_405 = FALSE; break;
			}
			while(0);
			if( $_405 === TRUE ) { $_407 = TRUE; break; }
			$result = $res_392;
			$this->pos = $pos_392;
			$_407 = FALSE; break;
		}
		while(0);
		if( $_407 === TRUE ) { $_409 = TRUE; break; }
		$result = $res_390;
		$this->pos = $pos_390;
		$_409 = FALSE; break;
	}
	while(0);
	if( $_409 === TRUE ) { return $this->finalise($result); }
	if( $_409 === FALSE) { return FALSE; }
}

public function type_expression_type_literal ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function type_expression_tuple_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'tuple' ;
		$self['types'] = $sub['types'] ;
	}

public function type_expression_record_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'record' ;
		$self['types'] = $sub['types'] ;
	}

public function type_expression_union_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'union' ;
		$self['types'] = $sub['types'] ;
	}

public function type_expression_intersection_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'intersection' ;
		$self['types'] = $sub['types'] ;
	}

public function type_expression_function_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'function' ;
		$self['parameter_type'] = $sub['parameter_type'] ;
		$self['return_type'] = $sub['return_type'] ;
	}

/* type-constant: type-expression */
protected $match_type_constant_typestack = array('type_constant');
function match_type_constant ($stack = array()) {
	$matchrule = "type_constant"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres );
		return $this->finalise($result);
	}
	else { return FALSE; }
}

public function type_constant__construct ( &$self ) {
		$self['node'] = 'constant';
		$self['type'] = 'type';
	}

public function type_constant_type_expression ( &$self, $sub ) {
		$self['type_value'] = $sub;
	}

/* enumeration-type-values: whitespace (enum-type-value whitespace "," whitespace)* enum-type-value whitespace */
protected $match_enumeration_type_values_typestack = array('enumeration_type_values');
function match_enumeration_type_values ($stack = array()) {
	$matchrule = "enumeration_type_values"; $result = $this->construct($matchrule, $matchrule, null);
	$_421 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_421 = FALSE; break; }
		while (true) {
			$res_418 = $result;
			$pos_418 = $this->pos;
			$_417 = NULL;
			do {
				$matcher = 'match_'.'enum_type_value'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_417 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_417 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_417 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_417 = FALSE; break; }
				$_417 = TRUE; break;
			}
			while(0);
			if( $_417 === FALSE) {
				$result = $res_418;
				$this->pos = $pos_418;
				unset( $res_418 );
				unset( $pos_418 );
				break;
			}
		}
		$matcher = 'match_'.'enum_type_value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_421 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_421 = FALSE; break; }
		$_421 = TRUE; break;
	}
	while(0);
	if( $_421 === TRUE ) { return $this->finalise($result); }
	if( $_421 === FALSE) { return FALSE; }
}

public function enumeration_type_values__construct ( &$self ) {
		$self['values'] = array() ;
	}

public function enumeration_type_values_enum_type_value ( &$self, $sub ) {
		$self['values'][] = $sub['text'] ;
	}

/* enumeration-type: whitespace enum-type-name whitespace "=" whitespace ":[" enumeration-type-values "]" */
protected $match_enumeration_type_typestack = array('enumeration_type');
function match_enumeration_type ($stack = array()) {
	$matchrule = "enumeration_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_431 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_431 = FALSE; break; }
		$matcher = 'match_'.'enum_type_name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_431 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_431 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
		}
		else { $_431 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_431 = FALSE; break; }
		if (( $subres = $this->literal( ':[' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_431 = FALSE; break; }
		$matcher = 'match_'.'enumeration_type_values'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_431 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_431 = FALSE; break; }
		$_431 = TRUE; break;
	}
	while(0);
	if( $_431 === TRUE ) { return $this->finalise($result); }
	if( $_431 === FALSE) { return FALSE; }
}

public function enumeration_type__construct ( &$self ) {
		$self['node'] = 'type_definition_term';
		$self['type'] = 'enumeration';
	}

public function enumeration_type_enum_type_name ( &$self, $sub ) {
		$self['type_name'] = $sub['text'] ;
	}

public function enumeration_type_enumeration_type_values ( &$self, $sub ) {
		$self['values'] = $sub['values'] ;
	}

/* alias-type: whitespace named-type whitespace "=" whitespace type-expression */
protected $match_alias_type_typestack = array('alias_type');
function match_alias_type ($stack = array()) {
	$matchrule = "alias_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_439 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_439 = FALSE; break; }
		$matcher = 'match_'.'named_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_439 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_439 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
		}
		else { $_439 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_439 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_439 = FALSE; break; }
		$_439 = TRUE; break;
	}
	while(0);
	if( $_439 === TRUE ) { return $this->finalise($result); }
	if( $_439 === FALSE) { return FALSE; }
}

public function alias_type__construct ( &$self ) {
		$self['node'] = 'type_definition_term';
		$self['type'] = 'alias';
	}

public function alias_type_named_type ( &$self, $sub ) {
		$self['type_name'] = $sub['text'] ;
	}

public function alias_type_type_expression ( &$self, $sub ) {
		$self['aliased_type'] = $sub ;
	}

/* subtype: whitespace named-type whitespace "<:" whitespace type-expression whitespace (error-type whitespace)? ("::" whitespace function-body)? */
protected $match_subtype_typestack = array('subtype');
function match_subtype ($stack = array()) {
	$matchrule = "subtype"; $result = $this->construct($matchrule, $matchrule, null);
	$_457 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_457 = FALSE; break; }
		$matcher = 'match_'.'named_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_457 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_457 = FALSE; break; }
		if (( $subres = $this->literal( '<:' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_457 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_457 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_457 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_457 = FALSE; break; }
		$res_451 = $result;
		$pos_451 = $this->pos;
		$_450 = NULL;
		do {
			$matcher = 'match_'.'error_type'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_450 = FALSE; break; }
			$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_450 = FALSE; break; }
			$_450 = TRUE; break;
		}
		while(0);
		if( $_450 === FALSE) {
			$result = $res_451;
			$this->pos = $pos_451;
			unset( $res_451 );
			unset( $pos_451 );
		}
		$res_456 = $result;
		$pos_456 = $this->pos;
		$_455 = NULL;
		do {
			if (( $subres = $this->literal( '::' ) ) !== FALSE) { $result["text"] .= $subres; }
			else { $_455 = FALSE; break; }
			$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_455 = FALSE; break; }
			$matcher = 'match_'.'function_body'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_455 = FALSE; break; }
			$_455 = TRUE; break;
		}
		while(0);
		if( $_455 === FALSE) {
			$result = $res_456;
			$this->pos = $pos_456;
			unset( $res_456 );
			unset( $pos_456 );
		}
		$_457 = TRUE; break;
	}
	while(0);
	if( $_457 === TRUE ) { return $this->finalise($result); }
	if( $_457 === FALSE) { return FALSE; }
}

public function subtype__construct ( &$self ) {
		$self['node'] = 'type_definition_term';
		$self['type'] = 'subtype';
		$self['any_error'] = false;
		$self['error_type'] = [
			'node' => 'type_term',
			'type' => 'nothing'
		];
		$self['function_body'] = [
			'node' => 'term',
			'term' => 'function_body',
			'body' => ['node' => 'term', 'term' => 'empty']
		];
	}

public function subtype_error_type ( &$self, $sub ) {
		$self['any_error'] = $sub['any_error'] ;
		$self['error_type'] = $sub['error_type'] ;
	}

public function subtype_named_type ( &$self, $sub ) {
		$self['type_name'] = $sub['text'] ;
	}

public function subtype_type_expression ( &$self, $sub ) {
		$self['base_type'] = $sub ;
	}

public function subtype_function_body ( &$self, $sub ) {
		$self['function_body'] = $sub ;
	}

/* empty-expression: "_" */
protected $match_empty_expression_typestack = array('empty_expression');
function match_empty_expression ($stack = array()) {
	$matchrule = "empty_expression"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '_') {
		$this->pos += 1;
		$result["text"] .= '_';
		return $this->finalise($result);
	}
	else { return FALSE; }
}

public function empty_expression__construct ( &$self) {
		$self['node'] = 'term' ;
		$self['term'] = 'empty' ;
	}

/* variable-name-expression: variable-name */
protected $match_variable_name_expression_typestack = array('variable_name_expression');
function match_variable_name_expression ($stack = array()) {
	$matchrule = "variable_name_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'variable_name'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres );
		return $this->finalise($result);
	}
	else { return FALSE; }
}

public function variable_name_expression__construct ( &$self) {
		$self['node'] = 'variable_name' ;
	}

public function variable_name_expression_variable_name ( &$self, $sub ) {
		$self['variable_name'] = $sub['text'];
	}

/* non-empty-tuple-expression: "[" whitespace (basic-expression whitespace "," whitespace)* basic-expression whitespace "]" */
protected $match_non_empty_tuple_expression_typestack = array('non_empty_tuple_expression');
function match_non_empty_tuple_expression ($stack = array()) {
	$matchrule = "non_empty_tuple_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_472 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_472 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_472 = FALSE; break; }
		while (true) {
			$res_468 = $result;
			$pos_468 = $this->pos;
			$_467 = NULL;
			do {
				$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_467 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_467 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_467 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_467 = FALSE; break; }
				$_467 = TRUE; break;
			}
			while(0);
			if( $_467 === FALSE) {
				$result = $res_468;
				$this->pos = $pos_468;
				unset( $res_468 );
				unset( $pos_468 );
				break;
			}
		}
		$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_472 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_472 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_472 = FALSE; break; }
		$_472 = TRUE; break;
	}
	while(0);
	if( $_472 === TRUE ) { return $this->finalise($result); }
	if( $_472 === FALSE) { return FALSE; }
}

public function non_empty_tuple_expression__construct ( &$self ) {
		$self['value'] = array();
	}

public function non_empty_tuple_expression_basic_expression ( &$self, $sub ) {
		$self['value'][] = $sub;
	}

/* tuple-expression: "[]" | non-empty-tuple-expression */
protected $match_tuple_expression_typestack = array('tuple_expression');
function match_tuple_expression ($stack = array()) {
	$matchrule = "tuple_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_477 = NULL;
	do {
		$res_474 = $result;
		$pos_474 = $this->pos;
		if (( $subres = $this->literal( '[]' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_477 = TRUE; break;
		}
		$result = $res_474;
		$this->pos = $pos_474;
		$matcher = 'match_'.'non_empty_tuple_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_477 = TRUE; break;
		}
		$result = $res_474;
		$this->pos = $pos_474;
		$_477 = FALSE; break;
	}
	while(0);
	if( $_477 === TRUE ) { return $this->finalise($result); }
	if( $_477 === FALSE) { return FALSE; }
}

public function tuple_expression__construct ( &$self ) {
		$self['node'] = 'term';
		$self['term'] = 'tuple';
		$self['value'] = array();
	}

public function tuple_expression_non_empty_tuple_expression ( &$self, $sub ) {
		$self['value'] = $sub['value'];
	}

/* record-key-expression-pair: record-key whitespace ":" whitespace basic-expression */
protected $match_record_key_expression_pair_typestack = array('record_key_expression_pair');
function match_record_key_expression_pair ($stack = array()) {
	$matchrule = "record_key_expression_pair"; $result = $this->construct($matchrule, $matchrule, null);
	$_484 = NULL;
	do {
		$matcher = 'match_'.'record_key'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_484 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_484 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_484 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_484 = FALSE; break; }
		$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_484 = FALSE; break; }
		$_484 = TRUE; break;
	}
	while(0);
	if( $_484 === TRUE ) { return $this->finalise($result); }
	if( $_484 === FALSE) { return FALSE; }
}

public function record_key_expression_pair_record_key ( &$self, $sub ) {
		$self['key'] = $sub['text'];
	}

public function record_key_expression_pair_basic_expression ( &$self, $sub ) {
		$self['value'] = $sub;
	}

/* non-empty-record-expression: "[" whitespace (record-key-expression-pair whitespace "," whitespace)* record-key-expression-pair whitespace "]" */
protected $match_non_empty_record_expression_typestack = array('non_empty_record_expression');
function match_non_empty_record_expression ($stack = array()) {
	$matchrule = "non_empty_record_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_497 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_497 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_497 = FALSE; break; }
		while (true) {
			$res_493 = $result;
			$pos_493 = $this->pos;
			$_492 = NULL;
			do {
				$matcher = 'match_'.'record_key_expression_pair'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_492 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_492 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_492 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_492 = FALSE; break; }
				$_492 = TRUE; break;
			}
			while(0);
			if( $_492 === FALSE) {
				$result = $res_493;
				$this->pos = $pos_493;
				unset( $res_493 );
				unset( $pos_493 );
				break;
			}
		}
		$matcher = 'match_'.'record_key_expression_pair'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_497 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_497 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_497 = FALSE; break; }
		$_497 = TRUE; break;
	}
	while(0);
	if( $_497 === TRUE ) { return $this->finalise($result); }
	if( $_497 === FALSE) { return FALSE; }
}

public function non_empty_record_expression__construct ( &$self ) {
		$self['value'] = array();
	}

public function non_empty_record_expression_record_key_expression_pair ( &$self, $sub ) {
		$self['value'][$sub['key']] = $sub['value'];
	}

/* record-expression: "[:]" | non-empty-record-expression */
protected $match_record_expression_typestack = array('record_expression');
function match_record_expression ($stack = array()) {
	$matchrule = "record_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_502 = NULL;
	do {
		$res_499 = $result;
		$pos_499 = $this->pos;
		if (( $subres = $this->literal( '[:]' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_502 = TRUE; break;
		}
		$result = $res_499;
		$this->pos = $pos_499;
		$matcher = 'match_'.'non_empty_record_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_502 = TRUE; break;
		}
		$result = $res_499;
		$this->pos = $pos_499;
		$_502 = FALSE; break;
	}
	while(0);
	if( $_502 === TRUE ) { return $this->finalise($result); }
	if( $_502 === FALSE) { return FALSE; }
}

public function record_expression__construct ( &$self ) {
		$self['node'] = 'term';
		$self['term'] = 'record';
		$self['value'] = array();
	}

public function record_expression_non_empty_record_expression ( &$self, $sub ) {
		$self['value'] = $sub['value'];
	}

/* value-expression: non-empty-tuple-type | tuple-expression | non-empty-record-type | record-expression | enum-value | type-literal | literal-constant | function-value */
protected $match_value_expression_typestack = array('value_expression');
function match_value_expression ($stack = array()) {
	$matchrule = "value_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_531 = NULL;
	do {
		$res_504 = $result;
		$pos_504 = $this->pos;
		$matcher = 'match_'.'non_empty_tuple_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_531 = TRUE; break;
		}
		$result = $res_504;
		$this->pos = $pos_504;
		$_529 = NULL;
		do {
			$res_506 = $result;
			$pos_506 = $this->pos;
			$matcher = 'match_'.'tuple_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_529 = TRUE; break;
			}
			$result = $res_506;
			$this->pos = $pos_506;
			$_527 = NULL;
			do {
				$res_508 = $result;
				$pos_508 = $this->pos;
				$matcher = 'match_'.'non_empty_record_type'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_527 = TRUE; break;
				}
				$result = $res_508;
				$this->pos = $pos_508;
				$_525 = NULL;
				do {
					$res_510 = $result;
					$pos_510 = $this->pos;
					$matcher = 'match_'.'record_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_525 = TRUE; break;
					}
					$result = $res_510;
					$this->pos = $pos_510;
					$_523 = NULL;
					do {
						$res_512 = $result;
						$pos_512 = $this->pos;
						$matcher = 'match_'.'enum_value'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_523 = TRUE; break;
						}
						$result = $res_512;
						$this->pos = $pos_512;
						$_521 = NULL;
						do {
							$res_514 = $result;
							$pos_514 = $this->pos;
							$matcher = 'match_'.'type_literal'; $key = $matcher; $pos = $this->pos;
							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
							if ($subres !== FALSE) {
								$this->store( $result, $subres );
								$_521 = TRUE; break;
							}
							$result = $res_514;
							$this->pos = $pos_514;
							$_519 = NULL;
							do {
								$res_516 = $result;
								$pos_516 = $this->pos;
								$matcher = 'match_'.'literal_constant'; $key = $matcher; $pos = $this->pos;
								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
								if ($subres !== FALSE) {
									$this->store( $result, $subres );
									$_519 = TRUE; break;
								}
								$result = $res_516;
								$this->pos = $pos_516;
								$matcher = 'match_'.'function_value'; $key = $matcher; $pos = $this->pos;
								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
								if ($subres !== FALSE) {
									$this->store( $result, $subres );
									$_519 = TRUE; break;
								}
								$result = $res_516;
								$this->pos = $pos_516;
								$_519 = FALSE; break;
							}
							while(0);
							if( $_519 === TRUE ) { $_521 = TRUE; break; }
							$result = $res_514;
							$this->pos = $pos_514;
							$_521 = FALSE; break;
						}
						while(0);
						if( $_521 === TRUE ) { $_523 = TRUE; break; }
						$result = $res_512;
						$this->pos = $pos_512;
						$_523 = FALSE; break;
					}
					while(0);
					if( $_523 === TRUE ) { $_525 = TRUE; break; }
					$result = $res_510;
					$this->pos = $pos_510;
					$_525 = FALSE; break;
				}
				while(0);
				if( $_525 === TRUE ) { $_527 = TRUE; break; }
				$result = $res_508;
				$this->pos = $pos_508;
				$_527 = FALSE; break;
			}
			while(0);
			if( $_527 === TRUE ) { $_529 = TRUE; break; }
			$result = $res_506;
			$this->pos = $pos_506;
			$_529 = FALSE; break;
		}
		while(0);
		if( $_529 === TRUE ) { $_531 = TRUE; break; }
		$result = $res_504;
		$this->pos = $pos_504;
		$_531 = FALSE; break;
	}
	while(0);
	if( $_531 === TRUE ) { return $this->finalise($result); }
	if( $_531 === FALSE) { return FALSE; }
}

public function value_expression__construct ( &$self) {
	}

public function value_expression_non_empty_tuple_type ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'constant' ;
		$self['constant'] = [
			'node' => 'constant',
			'type' => 'type',
			'type_value' => [
				'node' => 'type_term',
				'type' => 'tuple',
				'types' => $sub['types']
			]
		];
	}

public function value_expression_non_empty_record_type ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'constant' ;
		$self['constant'] = [
			'node' => 'constant',
			'type' => 'type',
			'type_value' => [
				'node' => 'type_term',
				'type' => 'record',
				'types' => $sub['types']
			]
		];
	}

public function value_expression_tuple_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'tuple' ;
		$self['value'] = $sub['value'] ;
	}

public function value_expression_record_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'record' ;
		$self['value'] = $sub['value'] ;
	}

public function value_expression_variable_name_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'variable_name' ;
		$self['variable'] = $sub;
	}

public function value_expression_literal_constant ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'constant' ;
		$self['constant'] = $sub;
	}

public function value_expression_enum_value ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'constant' ;
		$self['constant'] = $sub;
	}

public function value_expression_type_literal ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'constant' ;
		$self['constant'] = [
			'node' => 'constant',
			'type' => 'type',
			'type_value' => $sub
		];
	}

public function value_expression_function_value ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'constant' ;
		$self['constant'] = $sub;
	}

/* return-expression: "=>" whitespace basic-expression */
protected $match_return_expression_typestack = array('return_expression');
function match_return_expression ($stack = array()) {
	$matchrule = "return_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_536 = NULL;
	do {
		if (( $subres = $this->literal( '=>' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_536 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_536 = FALSE; break; }
		$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_536 = FALSE; break; }
		$_536 = TRUE; break;
	}
	while(0);
	if( $_536 === TRUE ) { return $this->finalise($result); }
	if( $_536 === FALSE) { return FALSE; }
}

public function return_expression__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['term'] = 'return' ;
	}

public function return_expression_basic_expression ( &$self, $sub ) {
		$self['returned_value'] = $sub ;
	}

/* throw-expression: "@" whitespace basic-expression */
protected $match_throw_expression_typestack = array('throw_expression');
function match_throw_expression ($stack = array()) {
	$matchrule = "throw_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_541 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '@') {
			$this->pos += 1;
			$result["text"] .= '@';
		}
		else { $_541 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_541 = FALSE; break; }
		$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_541 = FALSE; break; }
		$_541 = TRUE; break;
	}
	while(0);
	if( $_541 === TRUE ) { return $this->finalise($result); }
	if( $_541 === FALSE) { return FALSE; }
}

public function throw_expression__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['term'] = 'throw' ;
	}

public function throw_expression_basic_expression ( &$self, $sub ) {
		$self['thrown_value'] = $sub ;
	}

/* catch-expression: (any-error | explicit-error) whitespace sequence-expression */
protected $match_catch_expression_typestack = array('catch_expression');
function match_catch_expression ($stack = array()) {
	$matchrule = "catch_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_552 = NULL;
	do {
		$_548 = NULL;
		do {
			$_546 = NULL;
			do {
				$res_543 = $result;
				$pos_543 = $this->pos;
				$matcher = 'match_'.'any_error'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_546 = TRUE; break;
				}
				$result = $res_543;
				$this->pos = $pos_543;
				$matcher = 'match_'.'explicit_error'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_546 = TRUE; break;
				}
				$result = $res_543;
				$this->pos = $pos_543;
				$_546 = FALSE; break;
			}
			while(0);
			if( $_546 === FALSE) { $_548 = FALSE; break; }
			$_548 = TRUE; break;
		}
		while(0);
		if( $_548 === FALSE) { $_552 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_552 = FALSE; break; }
		$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_552 = FALSE; break; }
		$_552 = TRUE; break;
	}
	while(0);
	if( $_552 === TRUE ) { return $this->finalise($result); }
	if( $_552 === FALSE) { return FALSE; }
}

public function catch_expression_sequence_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

public function catch_expression_any_error ( &$self, $sub ) {
		$self['any_type'] = true ;
	}

public function catch_expression_explicit_error ( &$self, $sub ) {
		$self['any_type'] = false;
	}

/* match-expression-item: return-expression | throw-expression | loop-expression | match-expression | basic-expression | sequence-expression */
protected $match_match_expression_item_typestack = array('match_expression_item');
function match_match_expression_item ($stack = array()) {
	$matchrule = "match_expression_item"; $result = $this->construct($matchrule, $matchrule, null);
	$_573 = NULL;
	do {
		$res_554 = $result;
		$pos_554 = $this->pos;
		$matcher = 'match_'.'return_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_573 = TRUE; break;
		}
		$result = $res_554;
		$this->pos = $pos_554;
		$_571 = NULL;
		do {
			$res_556 = $result;
			$pos_556 = $this->pos;
			$matcher = 'match_'.'throw_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_571 = TRUE; break;
			}
			$result = $res_556;
			$this->pos = $pos_556;
			$_569 = NULL;
			do {
				$res_558 = $result;
				$pos_558 = $this->pos;
				$matcher = 'match_'.'loop_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_569 = TRUE; break;
				}
				$result = $res_558;
				$this->pos = $pos_558;
				$_567 = NULL;
				do {
					$res_560 = $result;
					$pos_560 = $this->pos;
					$matcher = 'match_'.'match_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_567 = TRUE; break;
					}
					$result = $res_560;
					$this->pos = $pos_560;
					$_565 = NULL;
					do {
						$res_562 = $result;
						$pos_562 = $this->pos;
						$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_565 = TRUE; break;
						}
						$result = $res_562;
						$this->pos = $pos_562;
						$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_565 = TRUE; break;
						}
						$result = $res_562;
						$this->pos = $pos_562;
						$_565 = FALSE; break;
					}
					while(0);
					if( $_565 === TRUE ) { $_567 = TRUE; break; }
					$result = $res_560;
					$this->pos = $pos_560;
					$_567 = FALSE; break;
				}
				while(0);
				if( $_567 === TRUE ) { $_569 = TRUE; break; }
				$result = $res_558;
				$this->pos = $pos_558;
				$_569 = FALSE; break;
			}
			while(0);
			if( $_569 === TRUE ) { $_571 = TRUE; break; }
			$result = $res_556;
			$this->pos = $pos_556;
			$_571 = FALSE; break;
		}
		while(0);
		if( $_571 === TRUE ) { $_573 = TRUE; break; }
		$result = $res_554;
		$this->pos = $pos_554;
		$_573 = FALSE; break;
	}
	while(0);
	if( $_573 === TRUE ) { return $this->finalise($result); }
	if( $_573 === FALSE) { return FALSE; }
}

public function match_expression_item_return_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'return' ;
		$self['returned_value'] = $sub['returned_value'] ;
	}

public function match_expression_item_throw_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'throw' ;
		$self['thrown_value'] = $sub['thrown_value'] ;
	}

public function match_expression_item_match_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'match' ;
		$self['target'] = $sub['target'] ;
		$self['parameters'] = $sub['parameters'] ;
	}

public function match_expression_item_loop_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'loop' ;
		$self['check_expression'] = $sub['check_expression'];
		$self['loop_expression'] = $sub['loop_expression'];
	}

public function match_expression_item_basic_expression ( &$self, $sub ) {
		$this->copy($self, $sub);
	}

public function match_expression_item_sequence_expression ( &$self, $sub ) {
		$this->copy($self, $sub);
	}

/* match-expression-pair: basic-expression whitespace ":" whitespace match-expression-item */
protected $match_match_expression_pair_typestack = array('match_expression_pair');
function match_match_expression_pair ($stack = array()) {
	$matchrule = "match_expression_pair"; $result = $this->construct($matchrule, $matchrule, null);
	$_580 = NULL;
	do {
		$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_580 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_580 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_580 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_580 = FALSE; break; }
		$matcher = 'match_'.'match_expression_item'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_580 = FALSE; break; }
		$_580 = TRUE; break;
	}
	while(0);
	if( $_580 === TRUE ) { return $this->finalise($result); }
	if( $_580 === FALSE) { return FALSE; }
}

public function match_expression_pair_basic_expression ( &$self, $sub ) {
		$self['matched_expression'] = $sub ;
	}

public function match_expression_pair_match_expression_item ( &$self, $sub ) {
		$self['value_expression'] = $sub ;
	}

/* default-match-expression-pair: "~" whitespace ":" whitespace match-expression-item */
protected $match_default_match_expression_pair_typestack = array('default_match_expression_pair');
function match_default_match_expression_pair ($stack = array()) {
	$matchrule = "default_match_expression_pair"; $result = $this->construct($matchrule, $matchrule, null);
	$_587 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '~') {
			$this->pos += 1;
			$result["text"] .= '~';
		}
		else { $_587 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_587 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_587 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_587 = FALSE; break; }
		$matcher = 'match_'.'match_expression_item'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_587 = FALSE; break; }
		$_587 = TRUE; break;
	}
	while(0);
	if( $_587 === TRUE ) { return $this->finalise($result); }
	if( $_587 === FALSE) { return FALSE; }
}

public function default_match_expression_pair_match_expression_item ( &$self, $sub ) {
		$self['value_expression'] = $sub ;
	}

/* final-match-expression-pair: match-expression-pair | default-match-expression-pair */
protected $match_final_match_expression_pair_typestack = array('final_match_expression_pair');
function match_final_match_expression_pair ($stack = array()) {
	$matchrule = "final_match_expression_pair"; $result = $this->construct($matchrule, $matchrule, null);
	$_592 = NULL;
	do {
		$res_589 = $result;
		$pos_589 = $this->pos;
		$matcher = 'match_'.'match_expression_pair'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_592 = TRUE; break;
		}
		$result = $res_589;
		$this->pos = $pos_589;
		$matcher = 'match_'.'default_match_expression_pair'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_592 = TRUE; break;
		}
		$result = $res_589;
		$this->pos = $pos_589;
		$_592 = FALSE; break;
	}
	while(0);
	if( $_592 === TRUE ) { return $this->finalise($result); }
	if( $_592 === FALSE) { return FALSE; }
}

public function final_match_expression_pair_match_expression_pair ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'match_pair' ;
		$self['matched_expression'] = $sub['matched_expression'] ;
		$self['value_expression'] = $sub['value_expression'] ;
	}

public function final_match_expression_pair_default_match_expression_pair ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'default_match_pair' ;
		$self['value_expression'] = $sub['value_expression'] ;
	}

/* match-expression-pairs: whitespace? "|"? whitespace? ( match-expression-pair whitespace? "|" whitespace? )* final-match-expression-pair whitespace? */
protected $match_match_expression_pairs_typestack = array('match_expression_pairs');
function match_match_expression_pairs ($stack = array()) {
	$matchrule = "match_expression_pairs"; $result = $this->construct($matchrule, $matchrule, null);
	$_605 = NULL;
	do {
		$res_594 = $result;
		$pos_594 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_594;
			$this->pos = $pos_594;
			unset( $res_594 );
			unset( $pos_594 );
		}
		$res_595 = $result;
		$pos_595 = $this->pos;
		if (substr($this->string,$this->pos,1) == '|') {
			$this->pos += 1;
			$result["text"] .= '|';
		}
		else {
			$result = $res_595;
			$this->pos = $pos_595;
			unset( $res_595 );
			unset( $pos_595 );
		}
		$res_596 = $result;
		$pos_596 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_596;
			$this->pos = $pos_596;
			unset( $res_596 );
			unset( $pos_596 );
		}
		while (true) {
			$res_602 = $result;
			$pos_602 = $this->pos;
			$_601 = NULL;
			do {
				$matcher = 'match_'.'match_expression_pair'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_601 = FALSE; break; }
				$res_598 = $result;
				$pos_598 = $this->pos;
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else {
					$result = $res_598;
					$this->pos = $pos_598;
					unset( $res_598 );
					unset( $pos_598 );
				}
				if (substr($this->string,$this->pos,1) == '|') {
					$this->pos += 1;
					$result["text"] .= '|';
				}
				else { $_601 = FALSE; break; }
				$res_600 = $result;
				$pos_600 = $this->pos;
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else {
					$result = $res_600;
					$this->pos = $pos_600;
					unset( $res_600 );
					unset( $pos_600 );
				}
				$_601 = TRUE; break;
			}
			while(0);
			if( $_601 === FALSE) {
				$result = $res_602;
				$this->pos = $pos_602;
				unset( $res_602 );
				unset( $pos_602 );
				break;
			}
		}
		$matcher = 'match_'.'final_match_expression_pair'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_605 = FALSE; break; }
		$res_604 = $result;
		$pos_604 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_604;
			$this->pos = $pos_604;
			unset( $res_604 );
			unset( $pos_604 );
		}
		$_605 = TRUE; break;
	}
	while(0);
	if( $_605 === TRUE ) { return $this->finalise($result); }
	if( $_605 === FALSE) { return FALSE; }
}

public function match_expression_pairs__construct ( &$self ) {
		$self['pairs'] = array() ;
	}

public function match_expression_pairs_match_expression_pair ( &$self, $sub ) {
		$self['pairs'][] = [
			'node' => 'term',
			'term' => 'match_pair',
			'matched_expression' => $sub['matched_expression'],
			'value_expression' => $sub['value_expression'],
		] ;
	}

public function match_expression_pairs_final_match_expression_pair ( &$self, $sub ) {
		$self['pairs'][] = $sub ;
	}

/* match-value-expression: "(" basic-expression ")" whitespace "?=" whitespace "{" match-expression-pairs "}" */
protected $match_match_value_expression_typestack = array('match_value_expression');
function match_match_value_expression ($stack = array()) {
	$matchrule = "match_value_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_616 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_616 = FALSE; break; }
		$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_616 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_616 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_616 = FALSE; break; }
		if (( $subres = $this->literal( '?=' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_616 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_616 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_616 = FALSE; break; }
		$matcher = 'match_'.'match_expression_pairs'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_616 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_616 = FALSE; break; }
		$_616 = TRUE; break;
	}
	while(0);
	if( $_616 === TRUE ) { return $this->finalise($result); }
	if( $_616 === FALSE) { return FALSE; }
}

public function match_value_expression__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['term'] = 'match_value' ;
		$self['terms'] = array() ;
	}

public function match_value_expression_basic_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

public function match_value_expression_match_expression_pairs ( &$self, $sub ) {
		$self['parameters'] = $sub['pairs'] ;
	}

/* match-if-expression-pairs: whitespace? "|"? whitespace? match-expression-item whitespace? "|" whitespace? match-expression-item whitespace? */
protected $match_match_if_expression_pairs_typestack = array('match_if_expression_pairs');
function match_match_if_expression_pairs ($stack = array()) {
	$matchrule = "match_if_expression_pairs"; $result = $this->construct($matchrule, $matchrule, null);
	$_627 = NULL;
	do {
		$res_618 = $result;
		$pos_618 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_618;
			$this->pos = $pos_618;
			unset( $res_618 );
			unset( $pos_618 );
		}
		$res_619 = $result;
		$pos_619 = $this->pos;
		if (substr($this->string,$this->pos,1) == '|') {
			$this->pos += 1;
			$result["text"] .= '|';
		}
		else {
			$result = $res_619;
			$this->pos = $pos_619;
			unset( $res_619 );
			unset( $pos_619 );
		}
		$res_620 = $result;
		$pos_620 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_620;
			$this->pos = $pos_620;
			unset( $res_620 );
			unset( $pos_620 );
		}
		$matcher = 'match_'.'match_expression_item'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_627 = FALSE; break; }
		$res_622 = $result;
		$pos_622 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_622;
			$this->pos = $pos_622;
			unset( $res_622 );
			unset( $pos_622 );
		}
		if (substr($this->string,$this->pos,1) == '|') {
			$this->pos += 1;
			$result["text"] .= '|';
		}
		else { $_627 = FALSE; break; }
		$res_624 = $result;
		$pos_624 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_624;
			$this->pos = $pos_624;
			unset( $res_624 );
			unset( $pos_624 );
		}
		$matcher = 'match_'.'match_expression_item'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_627 = FALSE; break; }
		$res_626 = $result;
		$pos_626 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_626;
			$this->pos = $pos_626;
			unset( $res_626 );
			unset( $pos_626 );
		}
		$_627 = TRUE; break;
	}
	while(0);
	if( $_627 === TRUE ) { return $this->finalise($result); }
	if( $_627 === FALSE) { return FALSE; }
}

public function match_if_expression_pairs__construct ( &$self ) {
		$self['pairs'] = array() ;
	}

public function match_if_expression_pairs_match_expression_item ( &$self, $sub ) {
		$self['pairs'][] = $sub;
	}

/* match-if-expression: "(" basic-expression ")" whitespace (("?==" whitespace "{" match-if-expression-pairs "}") | ("?" whitespace return-expression) | ("?" whitespace throw-expression)) */
protected $match_match_if_expression_typestack = array('match_if_expression');
function match_match_if_expression ($stack = array()) {
	$matchrule = "match_if_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_658 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_658 = FALSE; break; }
		$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_658 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_658 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_658 = FALSE; break; }
		$_656 = NULL;
		do {
			$_654 = NULL;
			do {
				$res_633 = $result;
				$pos_633 = $this->pos;
				$_639 = NULL;
				do {
					if (( $subres = $this->literal( '?==' ) ) !== FALSE) { $result["text"] .= $subres; }
					else { $_639 = FALSE; break; }
					$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_639 = FALSE; break; }
					if (substr($this->string,$this->pos,1) == '{') {
						$this->pos += 1;
						$result["text"] .= '{';
					}
					else { $_639 = FALSE; break; }
					$matcher = 'match_'.'match_if_expression_pairs'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_639 = FALSE; break; }
					if (substr($this->string,$this->pos,1) == '}') {
						$this->pos += 1;
						$result["text"] .= '}';
					}
					else { $_639 = FALSE; break; }
					$_639 = TRUE; break;
				}
				while(0);
				if( $_639 === TRUE ) { $_654 = TRUE; break; }
				$result = $res_633;
				$this->pos = $pos_633;
				$_652 = NULL;
				do {
					$res_641 = $result;
					$pos_641 = $this->pos;
					$_645 = NULL;
					do {
						if (substr($this->string,$this->pos,1) == '?') {
							$this->pos += 1;
							$result["text"] .= '?';
						}
						else { $_645 = FALSE; break; }
						$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
						}
						else { $_645 = FALSE; break; }
						$matcher = 'match_'.'return_expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
						}
						else { $_645 = FALSE; break; }
						$_645 = TRUE; break;
					}
					while(0);
					if( $_645 === TRUE ) { $_652 = TRUE; break; }
					$result = $res_641;
					$this->pos = $pos_641;
					$_650 = NULL;
					do {
						if (substr($this->string,$this->pos,1) == '?') {
							$this->pos += 1;
							$result["text"] .= '?';
						}
						else { $_650 = FALSE; break; }
						$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
						}
						else { $_650 = FALSE; break; }
						$matcher = 'match_'.'throw_expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
						}
						else { $_650 = FALSE; break; }
						$_650 = TRUE; break;
					}
					while(0);
					if( $_650 === TRUE ) { $_652 = TRUE; break; }
					$result = $res_641;
					$this->pos = $pos_641;
					$_652 = FALSE; break;
				}
				while(0);
				if( $_652 === TRUE ) { $_654 = TRUE; break; }
				$result = $res_633;
				$this->pos = $pos_633;
				$_654 = FALSE; break;
			}
			while(0);
			if( $_654 === FALSE) { $_656 = FALSE; break; }
			$_656 = TRUE; break;
		}
		while(0);
		if( $_656 === FALSE) { $_658 = FALSE; break; }
		$_658 = TRUE; break;
	}
	while(0);
	if( $_658 === TRUE ) { return $this->finalise($result); }
	if( $_658 === FALSE) { return FALSE; }
}

public function match_if_expression__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['term'] = 'match_if' ;
		$self['terms'] = array() ;
	}

public function match_if_expression_basic_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

public function match_if_expression_match_if_expression_pairs ( &$self, $sub ) {
		$self['parameters'] = $sub['pairs'] ;
	}

public function match_if_expression_throw_expression ( &$self, $sub ) {
		$self['parameters'] = [
			$sub,
			[
				'node' => 'term',
				'term' => 'constant',
				'constant' => [
					'node' => 'constant',
					'type' => 'literal',
					'literal' => [
						'node' => 'literal',
						'type' => 'null'
					]
				]
			]
		];
	}

public function match_if_expression_return_expression ( &$self, $sub ) {
		$self['parameters'] = [
			$sub,
			[
				'node' => 'term',
				'term' => 'constant',
				'constant' => [
					'node' => 'constant',
					'type' => 'literal',
					'literal' => [
						'node' => 'literal',
						'type' => 'null'
					]
				]
			]
		];
	}

/* match-true-expression: whitespace "??" whitespace "{" match-expression-pairs "}" */
protected $match_match_true_expression_typestack = array('match_true_expression');
function match_match_true_expression ($stack = array()) {
	$matchrule = "match_true_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_666 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_666 = FALSE; break; }
		if (( $subres = $this->literal( '??' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_666 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_666 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_666 = FALSE; break; }
		$matcher = 'match_'.'match_expression_pairs'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_666 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_666 = FALSE; break; }
		$_666 = TRUE; break;
	}
	while(0);
	if( $_666 === TRUE ) { return $this->finalise($result); }
	if( $_666 === FALSE) { return FALSE; }
}

public function match_true_expression__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['target'] = 'true' ;
		$self['term'] = 'match_true' ;
		$self['terms'] = array() ;
	}

public function match_true_expression_match_expression_pairs ( &$self, $sub ) {
		$self['parameters'] = $sub['pairs'] ;
	}

/* match-type-expression-pair: type-expression whitespace ":" whitespace match-expression-item */
protected $match_match_type_expression_pair_typestack = array('match_type_expression_pair');
function match_match_type_expression_pair ($stack = array()) {
	$matchrule = "match_type_expression_pair"; $result = $this->construct($matchrule, $matchrule, null);
	$_673 = NULL;
	do {
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_673 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_673 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_673 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_673 = FALSE; break; }
		$matcher = 'match_'.'match_expression_item'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_673 = FALSE; break; }
		$_673 = TRUE; break;
	}
	while(0);
	if( $_673 === TRUE ) { return $this->finalise($result); }
	if( $_673 === FALSE) { return FALSE; }
}

public function match_type_expression_pair_type_expression ( &$self, $sub ) {
		$self['matched_expression'] = $sub ;
	}

public function match_type_expression_pair_match_expression_item ( &$self, $sub ) {
		$self['value_expression'] = $sub ;
	}

/* final-match-type-expression-pair: match-type-expression-pair | default-match-expression-pair */
protected $match_final_match_type_expression_pair_typestack = array('final_match_type_expression_pair');
function match_final_match_type_expression_pair ($stack = array()) {
	$matchrule = "final_match_type_expression_pair"; $result = $this->construct($matchrule, $matchrule, null);
	$_678 = NULL;
	do {
		$res_675 = $result;
		$pos_675 = $this->pos;
		$matcher = 'match_'.'match_type_expression_pair'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_678 = TRUE; break;
		}
		$result = $res_675;
		$this->pos = $pos_675;
		$matcher = 'match_'.'default_match_expression_pair'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_678 = TRUE; break;
		}
		$result = $res_675;
		$this->pos = $pos_675;
		$_678 = FALSE; break;
	}
	while(0);
	if( $_678 === TRUE ) { return $this->finalise($result); }
	if( $_678 === FALSE) { return FALSE; }
}

public function final_match_type_expression_pair_match_type_expression_pair ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'match_type_pair' ;
		$self['matched_expression'] = $sub['matched_expression'] ;
		$self['value_expression'] = $sub['value_expression'] ;
	}

public function final_match_type_expression_pair_default_match_expression_pair ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'default_match_pair' ;
		$self['value_expression'] = $sub['value_expression'] ;
	}

/* match-type-expression-pairs: whitespace? "|"? whitespace? ( match-type-expression-pair whitespace? "|" whitespace? )* final-match-type-expression-pair whitespace? */
protected $match_match_type_expression_pairs_typestack = array('match_type_expression_pairs');
function match_match_type_expression_pairs ($stack = array()) {
	$matchrule = "match_type_expression_pairs"; $result = $this->construct($matchrule, $matchrule, null);
	$_691 = NULL;
	do {
		$res_680 = $result;
		$pos_680 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_680;
			$this->pos = $pos_680;
			unset( $res_680 );
			unset( $pos_680 );
		}
		$res_681 = $result;
		$pos_681 = $this->pos;
		if (substr($this->string,$this->pos,1) == '|') {
			$this->pos += 1;
			$result["text"] .= '|';
		}
		else {
			$result = $res_681;
			$this->pos = $pos_681;
			unset( $res_681 );
			unset( $pos_681 );
		}
		$res_682 = $result;
		$pos_682 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_682;
			$this->pos = $pos_682;
			unset( $res_682 );
			unset( $pos_682 );
		}
		while (true) {
			$res_688 = $result;
			$pos_688 = $this->pos;
			$_687 = NULL;
			do {
				$matcher = 'match_'.'match_type_expression_pair'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_687 = FALSE; break; }
				$res_684 = $result;
				$pos_684 = $this->pos;
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else {
					$result = $res_684;
					$this->pos = $pos_684;
					unset( $res_684 );
					unset( $pos_684 );
				}
				if (substr($this->string,$this->pos,1) == '|') {
					$this->pos += 1;
					$result["text"] .= '|';
				}
				else { $_687 = FALSE; break; }
				$res_686 = $result;
				$pos_686 = $this->pos;
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else {
					$result = $res_686;
					$this->pos = $pos_686;
					unset( $res_686 );
					unset( $pos_686 );
				}
				$_687 = TRUE; break;
			}
			while(0);
			if( $_687 === FALSE) {
				$result = $res_688;
				$this->pos = $pos_688;
				unset( $res_688 );
				unset( $pos_688 );
				break;
			}
		}
		$matcher = 'match_'.'final_match_type_expression_pair'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_691 = FALSE; break; }
		$res_690 = $result;
		$pos_690 = $this->pos;
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_690;
			$this->pos = $pos_690;
			unset( $res_690 );
			unset( $pos_690 );
		}
		$_691 = TRUE; break;
	}
	while(0);
	if( $_691 === TRUE ) { return $this->finalise($result); }
	if( $_691 === FALSE) { return FALSE; }
}

public function match_type_expression_pairs__construct ( &$self ) {
		$self['pairs'] = array() ;
	}

public function match_type_expression_pairs_match_type_expression_pair ( &$self, $sub ) {
		$self['pairs'][] = [
			'node' => 'term',
			'term' => 'match_type_pair',
			'matched_expression' => $sub['matched_expression'],
			'value_expression' => $sub['value_expression'],
		] ;
	}

public function match_type_expression_pairs_final_match_type_expression_pair ( &$self, $sub ) {
		$self['pairs'][] = $sub ;
	}

/* match-type-expression: "(" basic-expression ")" whitespace "?<:" whitespace "{" match-type-expression-pairs "}" */
protected $match_match_type_expression_typestack = array('match_type_expression');
function match_match_type_expression ($stack = array()) {
	$matchrule = "match_type_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_702 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_702 = FALSE; break; }
		$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_702 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_702 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_702 = FALSE; break; }
		if (( $subres = $this->literal( '?<:' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_702 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_702 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_702 = FALSE; break; }
		$matcher = 'match_'.'match_type_expression_pairs'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_702 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_702 = FALSE; break; }
		$_702 = TRUE; break;
	}
	while(0);
	if( $_702 === TRUE ) { return $this->finalise($result); }
	if( $_702 === FALSE) { return FALSE; }
}

public function match_type_expression__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['term'] = 'match_type' ;
		$self['terms'] = array() ;
	}

public function match_type_expression_basic_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

public function match_type_expression_match_type_expression_pairs ( &$self, $sub ) {
		$self['parameters'] = $sub['pairs'] ;
	}

/* match-expression: match-true-expression | match-if-expression | match-value-expression | match-type-expression */
protected $match_match_expression_typestack = array('match_expression');
function match_match_expression ($stack = array()) {
	$matchrule = "match_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_715 = NULL;
	do {
		$res_704 = $result;
		$pos_704 = $this->pos;
		$matcher = 'match_'.'match_true_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_715 = TRUE; break;
		}
		$result = $res_704;
		$this->pos = $pos_704;
		$_713 = NULL;
		do {
			$res_706 = $result;
			$pos_706 = $this->pos;
			$matcher = 'match_'.'match_if_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_713 = TRUE; break;
			}
			$result = $res_706;
			$this->pos = $pos_706;
			$_711 = NULL;
			do {
				$res_708 = $result;
				$pos_708 = $this->pos;
				$matcher = 'match_'.'match_value_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_711 = TRUE; break;
				}
				$result = $res_708;
				$this->pos = $pos_708;
				$matcher = 'match_'.'match_type_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_711 = TRUE; break;
				}
				$result = $res_708;
				$this->pos = $pos_708;
				$_711 = FALSE; break;
			}
			while(0);
			if( $_711 === TRUE ) { $_713 = TRUE; break; }
			$result = $res_706;
			$this->pos = $pos_706;
			$_713 = FALSE; break;
		}
		while(0);
		if( $_713 === TRUE ) { $_715 = TRUE; break; }
		$result = $res_704;
		$this->pos = $pos_704;
		$_715 = FALSE; break;
	}
	while(0);
	if( $_715 === TRUE ) { return $this->finalise($result); }
	if( $_715 === FALSE) { return FALSE; }
}

public function match_expression_match_true_expression ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function match_expression_match_if_expression ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function match_expression_match_value_expression ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function match_expression_match_type_expression ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

/* loop-expression-type: "?*" | "?+" */
protected $match_loop_expression_type_typestack = array('loop_expression_type');
function match_loop_expression_type ($stack = array()) {
	$matchrule = "loop_expression_type"; $result = $this->construct($matchrule, $matchrule, null);
	$_720 = NULL;
	do {
		$res_717 = $result;
		$pos_717 = $this->pos;
		if (( $subres = $this->literal( '?*' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_720 = TRUE; break;
		}
		$result = $res_717;
		$this->pos = $pos_717;
		if (( $subres = $this->literal( '?+' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_720 = TRUE; break;
		}
		$result = $res_717;
		$this->pos = $pos_717;
		$_720 = FALSE; break;
	}
	while(0);
	if( $_720 === TRUE ) { return $this->finalise($result); }
	if( $_720 === FALSE) { return FALSE; }
}


/* loop-expression: "(" basic-expression ")" whitespace loop-expression-type whitespace sequence-expression */
protected $match_loop_expression_typestack = array('loop_expression');
function match_loop_expression ($stack = array()) {
	$matchrule = "loop_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_729 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_729 = FALSE; break; }
		$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_729 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_729 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_729 = FALSE; break; }
		$matcher = 'match_'.'loop_expression_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_729 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_729 = FALSE; break; }
		$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_729 = FALSE; break; }
		$_729 = TRUE; break;
	}
	while(0);
	if( $_729 === TRUE ) { return $this->finalise($result); }
	if( $_729 === FALSE) { return FALSE; }
}

public function loop_expression__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['term'] = 'loop' ;
	}

public function loop_expression_basic_expression ( &$self, $sub ) {
		$self['check_expression'] = $sub ;
	}

public function loop_expression_loop_expression_type ( &$self, $sub ) {
		$self['loop_expression_type'] = $sub['text'] === '?*' ? 'while' : 'do-while';
	}

public function loop_expression_sequence_expression ( &$self, $sub ) {
		$self['loop_expression'] = $sub ;
	}

public function loop_expression_match_expression_item ( &$self, $sub ) {
		$self['loop_expression'] = $sub ;
	}

/* sequence-expression-item: return-expression | loop-expression | match-expression | basic-expression | throw-expression */
protected $match_sequence_expression_item_typestack = array('sequence_expression_item');
function match_sequence_expression_item ($stack = array()) {
	$matchrule = "sequence_expression_item"; $result = $this->construct($matchrule, $matchrule, null);
	$_746 = NULL;
	do {
		$res_731 = $result;
		$pos_731 = $this->pos;
		$matcher = 'match_'.'return_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_746 = TRUE; break;
		}
		$result = $res_731;
		$this->pos = $pos_731;
		$_744 = NULL;
		do {
			$res_733 = $result;
			$pos_733 = $this->pos;
			$matcher = 'match_'.'loop_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_744 = TRUE; break;
			}
			$result = $res_733;
			$this->pos = $pos_733;
			$_742 = NULL;
			do {
				$res_735 = $result;
				$pos_735 = $this->pos;
				$matcher = 'match_'.'match_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_742 = TRUE; break;
				}
				$result = $res_735;
				$this->pos = $pos_735;
				$_740 = NULL;
				do {
					$res_737 = $result;
					$pos_737 = $this->pos;
					$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_740 = TRUE; break;
					}
					$result = $res_737;
					$this->pos = $pos_737;
					$matcher = 'match_'.'throw_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_740 = TRUE; break;
					}
					$result = $res_737;
					$this->pos = $pos_737;
					$_740 = FALSE; break;
				}
				while(0);
				if( $_740 === TRUE ) { $_742 = TRUE; break; }
				$result = $res_735;
				$this->pos = $pos_735;
				$_742 = FALSE; break;
			}
			while(0);
			if( $_742 === TRUE ) { $_744 = TRUE; break; }
			$result = $res_733;
			$this->pos = $pos_733;
			$_744 = FALSE; break;
		}
		while(0);
		if( $_744 === TRUE ) { $_746 = TRUE; break; }
		$result = $res_731;
		$this->pos = $pos_731;
		$_746 = FALSE; break;
	}
	while(0);
	if( $_746 === TRUE ) { return $this->finalise($result); }
	if( $_746 === FALSE) { return FALSE; }
}

public function sequence_expression_item_return_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'return' ;
		$self['returned_value'] = $sub['returned_value'] ;
	}

public function sequence_expression_item_throw_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'throw' ;
		$self['thrown_value'] = $sub['thrown_value'] ;
	}

public function sequence_expression_item_match_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = $sub['term'] ;
		$self['target'] = $sub['target'] ;
		$self['parameters'] = $sub['parameters'] ;
	}

public function sequence_expression_item_loop_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'loop' ;
		$self['check_expression'] = $sub['check_expression'];
		$self['loop_expression_type'] = $sub['loop_expression_type'];
		$self['loop_expression'] = $sub['loop_expression'];
	}

public function sequence_expression_item_basic_expression ( &$self, $sub ) {
		$this->copy($self, $sub);
	}

/* sequence-expression: "{" whitespace ( sequence-expression-item whitespace ";" whitespace )* sequence-expression-item whitespace ( ";" )? whitespace "}" */
protected $match_sequence_expression_typestack = array('sequence_expression');
function match_sequence_expression ($stack = array()) {
	$matchrule = "sequence_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_763 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_763 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_763 = FALSE; break; }
		while (true) {
			$res_755 = $result;
			$pos_755 = $this->pos;
			$_754 = NULL;
			do {
				$matcher = 'match_'.'sequence_expression_item'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_754 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_754 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ';') {
					$this->pos += 1;
					$result["text"] .= ';';
				}
				else { $_754 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_754 = FALSE; break; }
				$_754 = TRUE; break;
			}
			while(0);
			if( $_754 === FALSE) {
				$result = $res_755;
				$this->pos = $pos_755;
				unset( $res_755 );
				unset( $pos_755 );
				break;
			}
		}
		$matcher = 'match_'.'sequence_expression_item'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_763 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_763 = FALSE; break; }
		$res_760 = $result;
		$pos_760 = $this->pos;
		$_759 = NULL;
		do {
			if (substr($this->string,$this->pos,1) == ';') {
				$this->pos += 1;
				$result["text"] .= ';';
			}
			else { $_759 = FALSE; break; }
			$_759 = TRUE; break;
		}
		while(0);
		if( $_759 === FALSE) {
			$result = $res_760;
			$this->pos = $pos_760;
			unset( $res_760 );
			unset( $pos_760 );
		}
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_763 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_763 = FALSE; break; }
		$_763 = TRUE; break;
	}
	while(0);
	if( $_763 === TRUE ) { return $this->finalise($result); }
	if( $_763 === FALSE) { return FALSE; }
}

public function sequence_expression__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['term'] = 'sequence' ;
		$self['sequence'] = array() ;
	}

public function sequence_expression_sequence_expression_item ( &$self, $sub ) {
		$self['sequence'][] = $sub ;
	}

/* variable-assignment-expression: variable-name whitespace "=" whitespace (basic-expression | match-expression | loop-expression | sequence-expression) */
protected $match_variable_assignment_expression_typestack = array('variable_assignment_expression');
function match_variable_assignment_expression ($stack = array()) {
	$matchrule = "variable_assignment_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_784 = NULL;
	do {
		$matcher = 'match_'.'variable_name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_784 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_784 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
		}
		else { $_784 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_784 = FALSE; break; }
		$_782 = NULL;
		do {
			$_780 = NULL;
			do {
				$res_769 = $result;
				$pos_769 = $this->pos;
				$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_780 = TRUE; break;
				}
				$result = $res_769;
				$this->pos = $pos_769;
				$_778 = NULL;
				do {
					$res_771 = $result;
					$pos_771 = $this->pos;
					$matcher = 'match_'.'match_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_778 = TRUE; break;
					}
					$result = $res_771;
					$this->pos = $pos_771;
					$_776 = NULL;
					do {
						$res_773 = $result;
						$pos_773 = $this->pos;
						$matcher = 'match_'.'loop_expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_776 = TRUE; break;
						}
						$result = $res_773;
						$this->pos = $pos_773;
						$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_776 = TRUE; break;
						}
						$result = $res_773;
						$this->pos = $pos_773;
						$_776 = FALSE; break;
					}
					while(0);
					if( $_776 === TRUE ) { $_778 = TRUE; break; }
					$result = $res_771;
					$this->pos = $pos_771;
					$_778 = FALSE; break;
				}
				while(0);
				if( $_778 === TRUE ) { $_780 = TRUE; break; }
				$result = $res_769;
				$this->pos = $pos_769;
				$_780 = FALSE; break;
			}
			while(0);
			if( $_780 === FALSE) { $_782 = FALSE; break; }
			$_782 = TRUE; break;
		}
		while(0);
		if( $_782 === FALSE) { $_784 = FALSE; break; }
		$_784 = TRUE; break;
	}
	while(0);
	if( $_784 === TRUE ) { return $this->finalise($result); }
	if( $_784 === FALSE) { return FALSE; }
}

public function variable_assignment_expression__construct ( &$self ) {
		$self['node'] = 'term';
		$self['term'] = 'variable_assignment';
	}

public function variable_assignment_expression_variable_name ( &$self, $sub ) {
		$self['variable_name'] = $sub['text'] ;
	}

public function variable_assignment_expression_basic_expression ( &$self, $sub ) {
		$self['value'] = $sub ;
	}

public function variable_assignment_expression_match_expression ( &$self, $sub ) {
		$self['value'] = $sub ;
	}

public function variable_assignment_expression_loop_expression ( &$self, $sub ) {
		$self['value'] = $sub ;
	}

public function variable_assignment_expression_sequence_expression ( &$self, $sub ) {
		$self['value'] = $sub ;
	}

/* call-parameter: basic-expression */
protected $match_call_parameter_typestack = array('call_parameter');
function match_call_parameter ($stack = array()) {
	$matchrule = "call_parameter"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres );
		return $this->finalise($result);
	}
	else { return FALSE; }
}

public function call_parameter_basic_expression ( &$self, $sub ) {
		$this->copy($self, $sub);
	}

/* non-empty-call-parameter-list: "(" whitespace call-parameter whitespace ")" */
protected $match_non_empty_call_parameter_list_typestack = array('non_empty_call_parameter_list');
function match_non_empty_call_parameter_list ($stack = array()) {
	$matchrule = "non_empty_call_parameter_list"; $result = $this->construct($matchrule, $matchrule, null);
	$_792 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_792 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_792 = FALSE; break; }
		$matcher = 'match_'.'call_parameter'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_792 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_792 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_792 = FALSE; break; }
		$_792 = TRUE; break;
	}
	while(0);
	if( $_792 === TRUE ) { return $this->finalise($result); }
	if( $_792 === FALSE) { return FALSE; }
}

public function non_empty_call_parameter_list__construct ( &$self ) {
		$self['parameter'] = array();
	}

public function non_empty_call_parameter_list_call_parameter ( &$self, $sub ) {
		$self['parameter'] = $sub;
	}

/* composite-parameter-list: tuple-expression | record-expression */
protected $match_composite_parameter_list_typestack = array('composite_parameter_list');
function match_composite_parameter_list ($stack = array()) {
	$matchrule = "composite_parameter_list"; $result = $this->construct($matchrule, $matchrule, null);
	$_797 = NULL;
	do {
		$res_794 = $result;
		$pos_794 = $this->pos;
		$matcher = 'match_'.'tuple_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_797 = TRUE; break;
		}
		$result = $res_794;
		$this->pos = $pos_794;
		$matcher = 'match_'.'record_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_797 = TRUE; break;
		}
		$result = $res_794;
		$this->pos = $pos_794;
		$_797 = FALSE; break;
	}
	while(0);
	if( $_797 === TRUE ) { return $this->finalise($result); }
	if( $_797 === FALSE) { return FALSE; }
}

public function composite_parameter_list__construct ( &$self ) {
		$self['parameter'] = array();
	}

public function composite_parameter_list_tuple_expression ( &$self, $sub ) {
		$self['parameter'] = $sub;
	}

public function composite_parameter_list_record_expression ( &$self, $sub ) {
		$self['parameter'] = $sub;
	}

/* call-parameter-list: "()" | non-empty-call-parameter-list | composite-parameter-list */
protected $match_call_parameter_list_typestack = array('call_parameter_list');
function match_call_parameter_list ($stack = array()) {
	$matchrule = "call_parameter_list"; $result = $this->construct($matchrule, $matchrule, null);
	$_806 = NULL;
	do {
		$res_799 = $result;
		$pos_799 = $this->pos;
		if (( $subres = $this->literal( '()' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_806 = TRUE; break;
		}
		$result = $res_799;
		$this->pos = $pos_799;
		$_804 = NULL;
		do {
			$res_801 = $result;
			$pos_801 = $this->pos;
			$matcher = 'match_'.'non_empty_call_parameter_list'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_804 = TRUE; break;
			}
			$result = $res_801;
			$this->pos = $pos_801;
			$matcher = 'match_'.'composite_parameter_list'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_804 = TRUE; break;
			}
			$result = $res_801;
			$this->pos = $pos_801;
			$_804 = FALSE; break;
		}
		while(0);
		if( $_804 === TRUE ) { $_806 = TRUE; break; }
		$result = $res_799;
		$this->pos = $pos_799;
		$_806 = FALSE; break;
	}
	while(0);
	if( $_806 === TRUE ) { return $this->finalise($result); }
	if( $_806 === FALSE) { return FALSE; }
}

public function call_parameter_list__construct ( &$self ) {
		$self['parameter'] = null;
	}

public function call_parameter_list_non_empty_call_parameter_list ( &$self, $sub ) {
		$self['parameter'] = $sub['parameter'];
	}

public function call_parameter_list_composite_parameter_list ( &$self, $sub ) {
		$self['parameter'] = $sub['parameter'];
	}

/* constructor-call-expression: named-type call-parameter-list */
protected $match_constructor_call_expression_typestack = array('constructor_call_expression');
function match_constructor_call_expression ($stack = array()) {
	$matchrule = "constructor_call_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_810 = NULL;
	do {
		$matcher = 'match_'.'named_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_810 = FALSE; break; }
		$matcher = 'match_'.'call_parameter_list'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_810 = FALSE; break; }
		$_810 = TRUE; break;
	}
	while(0);
	if( $_810 === TRUE ) { return $this->finalise($result); }
	if( $_810 === FALSE) { return FALSE; }
}

public function constructor_call_expression__construct ( &$self) {
		$self['parameter'] = null;
	}

public function constructor_call_expression_named_type ( &$self, $sub ) {
		$self['target_type'] = [
			'node' => 'type_term',
			'type' => 'type_name',
			'type_name' => $sub['text']
		];
	}

public function constructor_call_expression_call_parameter_list ( &$self, $sub ) {
		$self['parameter'] = $sub['parameter'];
	}

/* function-call-expression: (property-access-expression | sequence-expression | variable-name-expression) call-parameter-list */
protected $match_function_call_expression_typestack = array('function_call_expression');
function match_function_call_expression ($stack = array()) {
	$matchrule = "function_call_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_824 = NULL;
	do {
		$_821 = NULL;
		do {
			$_819 = NULL;
			do {
				$res_812 = $result;
				$pos_812 = $this->pos;
				$matcher = 'match_'.'property_access_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_819 = TRUE; break;
				}
				$result = $res_812;
				$this->pos = $pos_812;
				$_817 = NULL;
				do {
					$res_814 = $result;
					$pos_814 = $this->pos;
					$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_817 = TRUE; break;
					}
					$result = $res_814;
					$this->pos = $pos_814;
					$matcher = 'match_'.'variable_name_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_817 = TRUE; break;
					}
					$result = $res_814;
					$this->pos = $pos_814;
					$_817 = FALSE; break;
				}
				while(0);
				if( $_817 === TRUE ) { $_819 = TRUE; break; }
				$result = $res_812;
				$this->pos = $pos_812;
				$_819 = FALSE; break;
			}
			while(0);
			if( $_819 === FALSE) { $_821 = FALSE; break; }
			$_821 = TRUE; break;
		}
		while(0);
		if( $_821 === FALSE) { $_824 = FALSE; break; }
		$matcher = 'match_'.'call_parameter_list'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_824 = FALSE; break; }
		$_824 = TRUE; break;
	}
	while(0);
	if( $_824 === TRUE ) { return $this->finalise($result); }
	if( $_824 === FALSE) { return FALSE; }
}

public function function_call_expression__construct ( &$self) {
		$self['node'] = 'term' ;
		$self['term'] = 'function_call' ;
		$self['parameter'] = null;
	}

public function function_call_expression_property_access_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

public function function_call_expression_sequence_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

public function function_call_expression_variable_name_expression ( &$self, $sub ) {
		$self['target'] = [
			'node' => 'term',
			'term' => 'variable_name',
			'variable_name' => $sub['text']
		];
	}

public function function_call_expression_call_parameter_list ( &$self, $sub ) {
		$self['parameter'] = $sub['parameter'];
	}

/* property-access-expression-chain: (sequence-expression | variable-name-expression) ("." map-key)+ */
protected $match_property_access_expression_chain_typestack = array('property_access_expression_chain');
function match_property_access_expression_chain ($stack = array()) {
	$matchrule = "property_access_expression_chain"; $result = $this->construct($matchrule, $matchrule, null);
	$_837 = NULL;
	do {
		$_831 = NULL;
		do {
			$_829 = NULL;
			do {
				$res_826 = $result;
				$pos_826 = $this->pos;
				$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_829 = TRUE; break;
				}
				$result = $res_826;
				$this->pos = $pos_826;
				$matcher = 'match_'.'variable_name_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_829 = TRUE; break;
				}
				$result = $res_826;
				$this->pos = $pos_826;
				$_829 = FALSE; break;
			}
			while(0);
			if( $_829 === FALSE) { $_831 = FALSE; break; }
			$_831 = TRUE; break;
		}
		while(0);
		if( $_831 === FALSE) { $_837 = FALSE; break; }
		$count = 0;
		while (true) {
			$res_836 = $result;
			$pos_836 = $this->pos;
			$_835 = NULL;
			do {
				if (substr($this->string,$this->pos,1) == '.') {
					$this->pos += 1;
					$result["text"] .= '.';
				}
				else { $_835 = FALSE; break; }
				$matcher = 'match_'.'map_key'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_835 = FALSE; break; }
				$_835 = TRUE; break;
			}
			while(0);
			if( $_835 === FALSE) {
				$result = $res_836;
				$this->pos = $pos_836;
				unset( $res_836 );
				unset( $pos_836 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_837 = FALSE; break; }
		$_837 = TRUE; break;
	}
	while(0);
	if( $_837 === TRUE ) { return $this->finalise($result); }
	if( $_837 === FALSE) { return FALSE; }
}

public function property_access_expression_chain__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['term'] = 'property_access' ;
		$self['properties'] = [];
	}

public function property_access_expression_chain_variable_name_expression ( &$self, $sub ) {
		$self['target'] = [
			'node' => 'term',
			'term' => 'variable_name',
			'variable_name' => $sub['text']
		];
	}

public function property_access_expression_chain_sequence_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

public function property_access_expression_chain_map_key ( &$self, $sub ) {
		$self['properties'][] = $sub['text'];
	}

/* property-access-expression: property-access-expression-chain */
protected $match_property_access_expression_typestack = array('property_access_expression');
function match_property_access_expression ($stack = array()) {
	$matchrule = "property_access_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'property_access_expression_chain'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres );
		return $this->finalise($result);
	}
	else { return FALSE; }
}

public function property_access_expression_property_access_expression_chain ( &$self, $sub ) {
		$c = $sub['target'];
		foreach($sub['properties'] as $property) {
			$c = [
				'node' => 'term',
				'term' => 'property_access',
				'target' => $c,
				'property_name' => $property
			];
		}
		$this->copy($self, $c);
	}

/* method-call-expression: (property-access-expression | sequence-expression | variable-name-expression) whitespace "->" whitespace map-key (call-parameter-list)? */
protected $match_method_call_expression_typestack = array('method_call_expression');
function match_method_call_expression ($stack = array()) {
	$matchrule = "method_call_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_858 = NULL;
	do {
		$_849 = NULL;
		do {
			$_847 = NULL;
			do {
				$res_840 = $result;
				$pos_840 = $this->pos;
				$matcher = 'match_'.'property_access_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_847 = TRUE; break;
				}
				$result = $res_840;
				$this->pos = $pos_840;
				$_845 = NULL;
				do {
					$res_842 = $result;
					$pos_842 = $this->pos;
					$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_845 = TRUE; break;
					}
					$result = $res_842;
					$this->pos = $pos_842;
					$matcher = 'match_'.'variable_name_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_845 = TRUE; break;
					}
					$result = $res_842;
					$this->pos = $pos_842;
					$_845 = FALSE; break;
				}
				while(0);
				if( $_845 === TRUE ) { $_847 = TRUE; break; }
				$result = $res_840;
				$this->pos = $pos_840;
				$_847 = FALSE; break;
			}
			while(0);
			if( $_847 === FALSE) { $_849 = FALSE; break; }
			$_849 = TRUE; break;
		}
		while(0);
		if( $_849 === FALSE) { $_858 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_858 = FALSE; break; }
		if (( $subres = $this->literal( '->' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_858 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_858 = FALSE; break; }
		$matcher = 'match_'.'map_key'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_858 = FALSE; break; }
		$res_857 = $result;
		$pos_857 = $this->pos;
		$_856 = NULL;
		do {
			$matcher = 'match_'.'call_parameter_list'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_856 = FALSE; break; }
			$_856 = TRUE; break;
		}
		while(0);
		if( $_856 === FALSE) {
			$result = $res_857;
			$this->pos = $pos_857;
			unset( $res_857 );
			unset( $pos_857 );
		}
		$_858 = TRUE; break;
	}
	while(0);
	if( $_858 === TRUE ) { return $this->finalise($result); }
	if( $_858 === FALSE) { return FALSE; }
}

public function method_call_expression__construct ( &$self ) {
		$self['node'] = 'term' ;
		$self['term'] = 'method_call' ;
		$self['parameter'] = null ;
	}

public function method_call_expression_sequence_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

public function method_call_expression_property_access_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

public function method_call_expression_variable_name_expression ( &$self, $sub ) {
		$self['target'] = [
			'node' => 'term',
			'term' => 'variable_name',
			'variable_name' => $sub['text']
		];
	}

public function method_call_expression_map_key ( &$self, $sub ) {
		$self['method_name'] = $sub['text'];
	}

public function method_call_expression_call_parameter_list ( &$self, $sub ) {
		$self['parameter'] = $sub['parameter'];
	}

/* unary-op: "+" | "-" | "~" | "!" */
protected $match_unary_op_typestack = array('unary_op');
function match_unary_op ($stack = array()) {
	$matchrule = "unary_op"; $result = $this->construct($matchrule, $matchrule, null);
	$_871 = NULL;
	do {
		$res_860 = $result;
		$pos_860 = $this->pos;
		if (substr($this->string,$this->pos,1) == '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_871 = TRUE; break;
		}
		$result = $res_860;
		$this->pos = $pos_860;
		$_869 = NULL;
		do {
			$res_862 = $result;
			$pos_862 = $this->pos;
			if (substr($this->string,$this->pos,1) == '-') {
				$this->pos += 1;
				$result["text"] .= '-';
				$_869 = TRUE; break;
			}
			$result = $res_862;
			$this->pos = $pos_862;
			$_867 = NULL;
			do {
				$res_864 = $result;
				$pos_864 = $this->pos;
				if (substr($this->string,$this->pos,1) == '~') {
					$this->pos += 1;
					$result["text"] .= '~';
					$_867 = TRUE; break;
				}
				$result = $res_864;
				$this->pos = $pos_864;
				if (substr($this->string,$this->pos,1) == '!') {
					$this->pos += 1;
					$result["text"] .= '!';
					$_867 = TRUE; break;
				}
				$result = $res_864;
				$this->pos = $pos_864;
				$_867 = FALSE; break;
			}
			while(0);
			if( $_867 === TRUE ) { $_869 = TRUE; break; }
			$result = $res_862;
			$this->pos = $pos_862;
			$_869 = FALSE; break;
		}
		while(0);
		if( $_869 === TRUE ) { $_871 = TRUE; break; }
		$result = $res_860;
		$this->pos = $pos_860;
		$_871 = FALSE; break;
	}
	while(0);
	if( $_871 === TRUE ) { return $this->finalise($result); }
	if( $_871 === FALSE) { return FALSE; }
}


/* unary-expression-b: "(" whitespace unary-op whitespace sequence-expression whitespace ")" */
protected $match_unary_expression_b_typestack = array('unary_expression_b');
function match_unary_expression_b ($stack = array()) {
	$matchrule = "unary_expression_b"; $result = $this->construct($matchrule, $matchrule, null);
	$_880 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_880 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_880 = FALSE; break; }
		$matcher = 'match_'.'unary_op'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_880 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_880 = FALSE; break; }
		$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_880 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_880 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_880 = FALSE; break; }
		$_880 = TRUE; break;
	}
	while(0);
	if( $_880 === TRUE ) { return $this->finalise($result); }
	if( $_880 === FALSE) { return FALSE; }
}


/* unary-expression: unary-op whitespace sequence-expression */
protected $match_unary_expression_typestack = array('unary_expression');
function match_unary_expression ($stack = array()) {
	$matchrule = "unary_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_885 = NULL;
	do {
		$matcher = 'match_'.'unary_op'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_885 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_885 = FALSE; break; }
		$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_885 = FALSE; break; }
		$_885 = TRUE; break;
	}
	while(0);
	if( $_885 === TRUE ) { return $this->finalise($result); }
	if( $_885 === FALSE) { return FALSE; }
}

public function unary_expression_unary_op ( &$self, $sub ) {
		$self['operation'] = $sub['text'] ;
	}

public function unary_expression_sequence_expression ( &$self, $sub ) {
		$self['target'] = $sub ;
	}

/* binary-op: "**" | "||" | "&&" | "<=" | ">=" | "!=" | "==" | "//" | "<:" | "+" | "-" | "*" | "/" | "%" | "|" | "&" | "^" | "<" | ">" */
protected $match_binary_op_typestack = array('binary_op');
function match_binary_op ($stack = array()) {
	$matchrule = "binary_op"; $result = $this->construct($matchrule, $matchrule, null);
	$_958 = NULL;
	do {
		$res_887 = $result;
		$pos_887 = $this->pos;
		if (( $subres = $this->literal( '**' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_958 = TRUE; break;
		}
		$result = $res_887;
		$this->pos = $pos_887;
		$_956 = NULL;
		do {
			$res_889 = $result;
			$pos_889 = $this->pos;
			if (( $subres = $this->literal( '||' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_956 = TRUE; break;
			}
			$result = $res_889;
			$this->pos = $pos_889;
			$_954 = NULL;
			do {
				$res_891 = $result;
				$pos_891 = $this->pos;
				if (( $subres = $this->literal( '&&' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_954 = TRUE; break;
				}
				$result = $res_891;
				$this->pos = $pos_891;
				$_952 = NULL;
				do {
					$res_893 = $result;
					$pos_893 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_952 = TRUE; break;
					}
					$result = $res_893;
					$this->pos = $pos_893;
					$_950 = NULL;
					do {
						$res_895 = $result;
						$pos_895 = $this->pos;
						if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
							$result["text"] .= $subres;
							$_950 = TRUE; break;
						}
						$result = $res_895;
						$this->pos = $pos_895;
						$_948 = NULL;
						do {
							$res_897 = $result;
							$pos_897 = $this->pos;
							if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
								$result["text"] .= $subres;
								$_948 = TRUE; break;
							}
							$result = $res_897;
							$this->pos = $pos_897;
							$_946 = NULL;
							do {
								$res_899 = $result;
								$pos_899 = $this->pos;
								if (( $subres = $this->literal( '==' ) ) !== FALSE) {
									$result["text"] .= $subres;
									$_946 = TRUE; break;
								}
								$result = $res_899;
								$this->pos = $pos_899;
								$_944 = NULL;
								do {
									$res_901 = $result;
									$pos_901 = $this->pos;
									if (( $subres = $this->literal( '//' ) ) !== FALSE) {
										$result["text"] .= $subres;
										$_944 = TRUE; break;
									}
									$result = $res_901;
									$this->pos = $pos_901;
									$_942 = NULL;
									do {
										$res_903 = $result;
										$pos_903 = $this->pos;
										if (( $subres = $this->literal( '<:' ) ) !== FALSE) {
											$result["text"] .= $subres;
											$_942 = TRUE; break;
										}
										$result = $res_903;
										$this->pos = $pos_903;
										$_940 = NULL;
										do {
											$res_905 = $result;
											$pos_905 = $this->pos;
											if (substr($this->string,$this->pos,1) == '+') {
												$this->pos += 1;
												$result["text"] .= '+';
												$_940 = TRUE; break;
											}
											$result = $res_905;
											$this->pos = $pos_905;
											$_938 = NULL;
											do {
												$res_907 = $result;
												$pos_907 = $this->pos;
												if (substr($this->string,$this->pos,1) == '-') {
													$this->pos += 1;
													$result["text"] .= '-';
													$_938 = TRUE; break;
												}
												$result = $res_907;
												$this->pos = $pos_907;
												$_936 = NULL;
												do {
													$res_909 = $result;
													$pos_909 = $this->pos;
													if (substr($this->string,$this->pos,1) == '*') {
														$this->pos += 1;
														$result["text"] .= '*';
														$_936 = TRUE; break;
													}
													$result = $res_909;
													$this->pos = $pos_909;
													$_934 = NULL;
													do {
														$res_911 = $result;
														$pos_911 = $this->pos;
														if (substr($this->string,$this->pos,1) == '/') {
															$this->pos += 1;
															$result["text"] .= '/';
															$_934 = TRUE; break;
														}
														$result = $res_911;
														$this->pos = $pos_911;
														$_932 = NULL;
														do {
															$res_913 = $result;
															$pos_913 = $this->pos;
															if (substr($this->string,$this->pos,1) == '%') {
																$this->pos += 1;
																$result["text"] .= '%';
																$_932 = TRUE; break;
															}
															$result = $res_913;
															$this->pos = $pos_913;
															$_930 = NULL;
															do {
																$res_915 = $result;
																$pos_915 = $this->pos;
																if (substr($this->string,$this->pos,1) == '|') {
																	$this->pos += 1;
																	$result["text"] .= '|';
																	$_930 = TRUE; break;
																}
																$result = $res_915;
																$this->pos = $pos_915;
																$_928 = NULL;
																do {
																	$res_917 = $result;
																	$pos_917 = $this->pos;
																	if (substr($this->string,$this->pos,1) == '&') {
																		$this->pos += 1;
																		$result["text"] .= '&';
																		$_928 = TRUE; break;
																	}
																	$result = $res_917;
																	$this->pos = $pos_917;
																	$_926 = NULL;
																	do {
																		$res_919 = $result;
																		$pos_919 = $this->pos;
																		if (substr($this->string,$this->pos,1) == '^') {
																			$this->pos += 1;
																			$result["text"] .= '^';
																			$_926 = TRUE; break;
																		}
																		$result = $res_919;
																		$this->pos = $pos_919;
																		$_924 = NULL;
																		do {
																			$res_921 = $result;
																			$pos_921 = $this->pos;
																			if (substr($this->string,$this->pos,1) == '<') {
																				$this->pos += 1;
																				$result["text"] .= '<';
																				$_924 = TRUE; break;
																			}
																			$result = $res_921;
																			$this->pos = $pos_921;
																			if (substr($this->string,$this->pos,1) == '>') {
																				$this->pos += 1;
																				$result["text"] .= '>';
																				$_924 = TRUE; break;
																			}
																			$result = $res_921;
																			$this->pos = $pos_921;
																			$_924 = FALSE; break;
																		}
																		while(0);
																		if( $_924 === TRUE ) { $_926 = TRUE; break; }
																		$result = $res_919;
																		$this->pos = $pos_919;
																		$_926 = FALSE; break;
																	}
																	while(0);
																	if( $_926 === TRUE ) { $_928 = TRUE; break; }
																	$result = $res_917;
																	$this->pos = $pos_917;
																	$_928 = FALSE; break;
																}
																while(0);
																if( $_928 === TRUE ) { $_930 = TRUE; break; }
																$result = $res_915;
																$this->pos = $pos_915;
																$_930 = FALSE; break;
															}
															while(0);
															if( $_930 === TRUE ) { $_932 = TRUE; break; }
															$result = $res_913;
															$this->pos = $pos_913;
															$_932 = FALSE; break;
														}
														while(0);
														if( $_932 === TRUE ) { $_934 = TRUE; break; }
														$result = $res_911;
														$this->pos = $pos_911;
														$_934 = FALSE; break;
													}
													while(0);
													if( $_934 === TRUE ) { $_936 = TRUE; break; }
													$result = $res_909;
													$this->pos = $pos_909;
													$_936 = FALSE; break;
												}
												while(0);
												if( $_936 === TRUE ) { $_938 = TRUE; break; }
												$result = $res_907;
												$this->pos = $pos_907;
												$_938 = FALSE; break;
											}
											while(0);
											if( $_938 === TRUE ) { $_940 = TRUE; break; }
											$result = $res_905;
											$this->pos = $pos_905;
											$_940 = FALSE; break;
										}
										while(0);
										if( $_940 === TRUE ) { $_942 = TRUE; break; }
										$result = $res_903;
										$this->pos = $pos_903;
										$_942 = FALSE; break;
									}
									while(0);
									if( $_942 === TRUE ) { $_944 = TRUE; break; }
									$result = $res_901;
									$this->pos = $pos_901;
									$_944 = FALSE; break;
								}
								while(0);
								if( $_944 === TRUE ) { $_946 = TRUE; break; }
								$result = $res_899;
								$this->pos = $pos_899;
								$_946 = FALSE; break;
							}
							while(0);
							if( $_946 === TRUE ) { $_948 = TRUE; break; }
							$result = $res_897;
							$this->pos = $pos_897;
							$_948 = FALSE; break;
						}
						while(0);
						if( $_948 === TRUE ) { $_950 = TRUE; break; }
						$result = $res_895;
						$this->pos = $pos_895;
						$_950 = FALSE; break;
					}
					while(0);
					if( $_950 === TRUE ) { $_952 = TRUE; break; }
					$result = $res_893;
					$this->pos = $pos_893;
					$_952 = FALSE; break;
				}
				while(0);
				if( $_952 === TRUE ) { $_954 = TRUE; break; }
				$result = $res_891;
				$this->pos = $pos_891;
				$_954 = FALSE; break;
			}
			while(0);
			if( $_954 === TRUE ) { $_956 = TRUE; break; }
			$result = $res_889;
			$this->pos = $pos_889;
			$_956 = FALSE; break;
		}
		while(0);
		if( $_956 === TRUE ) { $_958 = TRUE; break; }
		$result = $res_887;
		$this->pos = $pos_887;
		$_958 = FALSE; break;
	}
	while(0);
	if( $_958 === TRUE ) { return $this->finalise($result); }
	if( $_958 === FALSE) { return FALSE; }
}


/* binary-expression-b: "(" whitespace sequence-expression whitespace binary-op whitespace sequence-expression whitespace ")" */
protected $match_binary_expression_b_typestack = array('binary_expression_b');
function match_binary_expression_b ($stack = array()) {
	$matchrule = "binary_expression_b"; $result = $this->construct($matchrule, $matchrule, null);
	$_969 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_969 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_969 = FALSE; break; }
		$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_969 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_969 = FALSE; break; }
		$matcher = 'match_'.'binary_op'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_969 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_969 = FALSE; break; }
		$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_969 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_969 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_969 = FALSE; break; }
		$_969 = TRUE; break;
	}
	while(0);
	if( $_969 === TRUE ) { return $this->finalise($result); }
	if( $_969 === FALSE) { return FALSE; }
}


/* binary-expression: sequence-expression whitespace binary-op whitespace (sequence-expression | literal-constant) */
protected $match_binary_expression_typestack = array('binary_expression');
function match_binary_expression ($stack = array()) {
	$matchrule = "binary_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_982 = NULL;
	do {
		$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_982 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_982 = FALSE; break; }
		$matcher = 'match_'.'binary_op'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_982 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_982 = FALSE; break; }
		$_980 = NULL;
		do {
			$_978 = NULL;
			do {
				$res_975 = $result;
				$pos_975 = $this->pos;
				$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_978 = TRUE; break;
				}
				$result = $res_975;
				$this->pos = $pos_975;
				$matcher = 'match_'.'literal_constant'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_978 = TRUE; break;
				}
				$result = $res_975;
				$this->pos = $pos_975;
				$_978 = FALSE; break;
			}
			while(0);
			if( $_978 === FALSE) { $_980 = FALSE; break; }
			$_980 = TRUE; break;
		}
		while(0);
		if( $_980 === FALSE) { $_982 = FALSE; break; }
		$_982 = TRUE; break;
	}
	while(0);
	if( $_982 === TRUE ) { return $this->finalise($result); }
	if( $_982 === FALSE) { return FALSE; }
}

public function binary_expression__construct ( &$self ) {
		$self['terms'] = array() ;
	}

public function binary_expression_binary_op ( &$self, $sub ) {
		$self['operation'] = $sub['text'] ;
	}

public function binary_expression_sequence_expression ( &$self, $sub ) {
		$self['terms'][] = $sub ;
	}

public function binary_expression_literal_constant ( &$self, $sub ) {
		$self['terms'][] = [
			'node' => 'term',
			'term' => 'constant',
			'constant' => $sub
		];
	}

/* basic-expression: catch-expression | constructor-call-expression | function-call-expression | method-call-expression | property-access-expression | value-expression | variable-assignment-expression | variable-name-expression | unary-expression | binary-expression */
protected $match_basic_expression_typestack = array('basic_expression');
function match_basic_expression ($stack = array()) {
	$matchrule = "basic_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_1019 = NULL;
	do {
		$res_984 = $result;
		$pos_984 = $this->pos;
		$matcher = 'match_'.'catch_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_1019 = TRUE; break;
		}
		$result = $res_984;
		$this->pos = $pos_984;
		$_1017 = NULL;
		do {
			$res_986 = $result;
			$pos_986 = $this->pos;
			$matcher = 'match_'.'constructor_call_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1017 = TRUE; break;
			}
			$result = $res_986;
			$this->pos = $pos_986;
			$_1015 = NULL;
			do {
				$res_988 = $result;
				$pos_988 = $this->pos;
				$matcher = 'match_'.'function_call_expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_1015 = TRUE; break;
				}
				$result = $res_988;
				$this->pos = $pos_988;
				$_1013 = NULL;
				do {
					$res_990 = $result;
					$pos_990 = $this->pos;
					$matcher = 'match_'.'method_call_expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_1013 = TRUE; break;
					}
					$result = $res_990;
					$this->pos = $pos_990;
					$_1011 = NULL;
					do {
						$res_992 = $result;
						$pos_992 = $this->pos;
						$matcher = 'match_'.'property_access_expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_1011 = TRUE; break;
						}
						$result = $res_992;
						$this->pos = $pos_992;
						$_1009 = NULL;
						do {
							$res_994 = $result;
							$pos_994 = $this->pos;
							$matcher = 'match_'.'value_expression'; $key = $matcher; $pos = $this->pos;
							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
							if ($subres !== FALSE) {
								$this->store( $result, $subres );
								$_1009 = TRUE; break;
							}
							$result = $res_994;
							$this->pos = $pos_994;
							$_1007 = NULL;
							do {
								$res_996 = $result;
								$pos_996 = $this->pos;
								$matcher = 'match_'.'variable_assignment_expression'; $key = $matcher; $pos = $this->pos;
								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
								if ($subres !== FALSE) {
									$this->store( $result, $subres );
									$_1007 = TRUE; break;
								}
								$result = $res_996;
								$this->pos = $pos_996;
								$_1005 = NULL;
								do {
									$res_998 = $result;
									$pos_998 = $this->pos;
									$matcher = 'match_'.'variable_name_expression'; $key = $matcher; $pos = $this->pos;
									$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
									if ($subres !== FALSE) {
										$this->store( $result, $subres );
										$_1005 = TRUE; break;
									}
									$result = $res_998;
									$this->pos = $pos_998;
									$_1003 = NULL;
									do {
										$res_1000 = $result;
										$pos_1000 = $this->pos;
										$matcher = 'match_'.'unary_expression'; $key = $matcher; $pos = $this->pos;
										$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
										if ($subres !== FALSE) {
											$this->store( $result, $subres );
											$_1003 = TRUE; break;
										}
										$result = $res_1000;
										$this->pos = $pos_1000;
										$matcher = 'match_'.'binary_expression'; $key = $matcher; $pos = $this->pos;
										$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
										if ($subres !== FALSE) {
											$this->store( $result, $subres );
											$_1003 = TRUE; break;
										}
										$result = $res_1000;
										$this->pos = $pos_1000;
										$_1003 = FALSE; break;
									}
									while(0);
									if( $_1003 === TRUE ) { $_1005 = TRUE; break; }
									$result = $res_998;
									$this->pos = $pos_998;
									$_1005 = FALSE; break;
								}
								while(0);
								if( $_1005 === TRUE ) { $_1007 = TRUE; break; }
								$result = $res_996;
								$this->pos = $pos_996;
								$_1007 = FALSE; break;
							}
							while(0);
							if( $_1007 === TRUE ) { $_1009 = TRUE; break; }
							$result = $res_994;
							$this->pos = $pos_994;
							$_1009 = FALSE; break;
						}
						while(0);
						if( $_1009 === TRUE ) { $_1011 = TRUE; break; }
						$result = $res_992;
						$this->pos = $pos_992;
						$_1011 = FALSE; break;
					}
					while(0);
					if( $_1011 === TRUE ) { $_1013 = TRUE; break; }
					$result = $res_990;
					$this->pos = $pos_990;
					$_1013 = FALSE; break;
				}
				while(0);
				if( $_1013 === TRUE ) { $_1015 = TRUE; break; }
				$result = $res_988;
				$this->pos = $pos_988;
				$_1015 = FALSE; break;
			}
			while(0);
			if( $_1015 === TRUE ) { $_1017 = TRUE; break; }
			$result = $res_986;
			$this->pos = $pos_986;
			$_1017 = FALSE; break;
		}
		while(0);
		if( $_1017 === TRUE ) { $_1019 = TRUE; break; }
		$result = $res_984;
		$this->pos = $pos_984;
		$_1019 = FALSE; break;
	}
	while(0);
	if( $_1019 === TRUE ) { return $this->finalise($result); }
	if( $_1019 === FALSE) { return FALSE; }
}

public function basic_expression__construct ( &$self ) {
	}

public function basic_expression_value_expression ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function basic_expression_constructor_call_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'constructor_call' ;
		$self['target_type'] = $sub['target_type'] ;
		$self['parameter'] = $sub['parameter'] ;
	}

public function basic_expression_function_call_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'function_call' ;
		$self['target'] = $sub['target'] ;
		$self['parameter'] = $sub['parameter'] ;
	}

public function basic_expression_property_access_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'property_access' ;
		$self['target'] = $sub['target'] ;
		$self['property_name'] = $sub['property_name'] ;
	}

public function basic_expression_method_call_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'method_call' ;
		$self['target'] = $sub['target'] ;
		$self['method_name'] = $sub['method_name'] ;
		$self['parameter'] = $sub['parameter'] ;
	}

public function basic_expression_catch_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'catch' ;
		$self['any_type'] = $sub['any_type'] ;
		$self['target'] = $sub['target'] ;
	}

public function basic_expression_variable_name_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'variable_name' ;
		$self['variable'] = $sub;
	}

public function basic_expression_variable_assignment_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'variable_assignment' ;
		$self['variable_name'] = $sub['variable_name'] ;
		$self['value'] = $sub['value'] ;
	}

public function basic_expression_unary_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'unary' ;
		$self['operation'] = $sub['operation'] ;
		$self['target'] = $sub['target'] ;
	}

public function basic_expression_binary_expression ( &$self, $sub ) {
		$self['node'] = 'term' ;
		$self['term'] = 'binary' ;
		$self['operation'] = $sub['operation'] ;
		$self['first_target'] = $sub['terms'][0] ;
		$self['second_target'] = $sub['terms'][1] ;
	}

/* function-body: sequence-expression | basic-expression | empty-expression */
protected $match_function_body_typestack = array('function_body');
function match_function_body ($stack = array()) {
	$matchrule = "function_body"; $result = $this->construct($matchrule, $matchrule, null);
	$_1028 = NULL;
	do {
		$res_1021 = $result;
		$pos_1021 = $this->pos;
		$matcher = 'match_'.'sequence_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_1028 = TRUE; break;
		}
		$result = $res_1021;
		$this->pos = $pos_1021;
		$_1026 = NULL;
		do {
			$res_1023 = $result;
			$pos_1023 = $this->pos;
			$matcher = 'match_'.'basic_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1026 = TRUE; break;
			}
			$result = $res_1023;
			$this->pos = $pos_1023;
			$matcher = 'match_'.'empty_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1026 = TRUE; break;
			}
			$result = $res_1023;
			$this->pos = $pos_1023;
			$_1026 = FALSE; break;
		}
		while(0);
		if( $_1026 === TRUE ) { $_1028 = TRUE; break; }
		$result = $res_1021;
		$this->pos = $pos_1021;
		$_1028 = FALSE; break;
	}
	while(0);
	if( $_1028 === TRUE ) { return $this->finalise($result); }
	if( $_1028 === FALSE) { return FALSE; }
}

public function function_body__construct ( &$self ) {
		$self['node'] = 'term';
		$self['term'] = 'function_body';
	}

public function function_body_throw_expression ( &$self, $sub ) {
		$self['body'] = $sub ;
	}

public function function_body_match_expression ( &$self, $sub ) {
		$self['body'] = $sub ;
	}

public function function_body_loop_expression ( &$self, $sub ) {
		$self['body'] = $sub ;
	}

public function function_body_sequence_expression ( &$self, $sub ) {
		$self['body'] = $sub ;
	}

public function function_body_basic_expression ( &$self, $sub ) {
		$self['body'] = $sub ;
	}

public function function_body_empty_expression ( &$self, $sub ) {
		$self['body'] = $sub ;
	}

/* function-value: '^' type-expression whitespace "=>" whitespace return-type whitespace "::" whitespace function-body */
protected $match_function_value_typestack = array('function_value');
function match_function_value ($stack = array()) {
	$matchrule = "function_value"; $result = $this->construct($matchrule, $matchrule, null);
	$_1040 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '^') {
			$this->pos += 1;
			$result["text"] .= '^';
		}
		else { $_1040 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1040 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1040 = FALSE; break; }
		if (( $subres = $this->literal( '=>' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_1040 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1040 = FALSE; break; }
		$matcher = 'match_'.'return_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1040 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1040 = FALSE; break; }
		if (( $subres = $this->literal( '::' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_1040 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1040 = FALSE; break; }
		$matcher = 'match_'.'function_body'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1040 = FALSE; break; }
		$_1040 = TRUE; break;
	}
	while(0);
	if( $_1040 === TRUE ) { return $this->finalise($result); }
	if( $_1040 === FALSE) { return FALSE; }
}

public function function_value__construct ( &$self ) {
		$self['node'] = 'constant';
		$self['type'] = 'function';
		$self['value'] = [];
	}

public function function_value_type_expression ( &$self, $sub ) {
		$self['value']['parameter_type'] = $sub ;
	}

public function function_value_return_type ( &$self, $sub ) {
		$self['value']['return_type'] = $sub ;
	}

public function function_value_function_body ( &$self, $sub ) {
		$self['value']['function_body'] = $sub ;
	}

/* function-constant: '^' type-expression whitespace "=>" whitespace return-type whitespace "::" whitespace function-body */
protected $match_function_constant_typestack = array('function_constant');
function match_function_constant ($stack = array()) {
	$matchrule = "function_constant"; $result = $this->construct($matchrule, $matchrule, null);
	$_1052 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '^') {
			$this->pos += 1;
			$result["text"] .= '^';
		}
		else { $_1052 = FALSE; break; }
		$matcher = 'match_'.'type_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1052 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1052 = FALSE; break; }
		if (( $subres = $this->literal( '=>' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_1052 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1052 = FALSE; break; }
		$matcher = 'match_'.'return_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1052 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1052 = FALSE; break; }
		if (( $subres = $this->literal( '::' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_1052 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1052 = FALSE; break; }
		$matcher = 'match_'.'function_body'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1052 = FALSE; break; }
		$_1052 = TRUE; break;
	}
	while(0);
	if( $_1052 === TRUE ) { return $this->finalise($result); }
	if( $_1052 === FALSE) { return FALSE; }
}

public function function_constant__construct ( &$self ) {
		$self['node'] = 'constant';
		$self['type'] = 'function';
		$self['value'] = [];
	}

public function function_constant_type_expression ( &$self, $sub ) {
		$self['value']['parameter_type'] = $sub ;
	}

public function function_constant_return_type ( &$self, $sub ) {
		$self['value']['return_type'] = $sub ;
	}

public function function_constant_function_body ( &$self, $sub ) {
		$self['value']['function_body'] = $sub ;
	}

/* type-definition-expression: enumeration-type | alias-type | subtype */
protected $match_type_definition_expression_typestack = array('type_definition_expression');
function match_type_definition_expression ($stack = array()) {
	$matchrule = "type_definition_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_1061 = NULL;
	do {
		$res_1054 = $result;
		$pos_1054 = $this->pos;
		$matcher = 'match_'.'enumeration_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_1061 = TRUE; break;
		}
		$result = $res_1054;
		$this->pos = $pos_1054;
		$_1059 = NULL;
		do {
			$res_1056 = $result;
			$pos_1056 = $this->pos;
			$matcher = 'match_'.'alias_type'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1059 = TRUE; break;
			}
			$result = $res_1056;
			$this->pos = $pos_1056;
			$matcher = 'match_'.'subtype'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1059 = TRUE; break;
			}
			$result = $res_1056;
			$this->pos = $pos_1056;
			$_1059 = FALSE; break;
		}
		while(0);
		if( $_1059 === TRUE ) { $_1061 = TRUE; break; }
		$result = $res_1054;
		$this->pos = $pos_1054;
		$_1061 = FALSE; break;
	}
	while(0);
	if( $_1061 === TRUE ) { return $this->finalise($result); }
	if( $_1061 === FALSE) { return FALSE; }
}

public function type_definition_expression__construct ( &$self ) {
		$self['node'] = 'module_expression';
		$self['expression_type'] = 'type_definition_term';
	}

public function type_definition_expression_alias_type ( &$self, $sub ) {
		$self['type_definition'] = $sub;
	}

public function type_definition_expression_subtype ( &$self, $sub ) {
		$self['type_definition'] = $sub;
	}

public function type_definition_expression_enumeration_type ( &$self, $sub ) {
		$self['type_definition'] = $sub;
	}

/* enum-value: enum-type-name "." enum-type-value */
protected $match_enum_value_typestack = array('enum_value');
function match_enum_value ($stack = array()) {
	$matchrule = "enum_value"; $result = $this->construct($matchrule, $matchrule, null);
	$_1066 = NULL;
	do {
		$matcher = 'match_'.'enum_type_name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1066 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '.') {
			$this->pos += 1;
			$result["text"] .= '.';
		}
		else { $_1066 = FALSE; break; }
		$matcher = 'match_'.'enum_type_value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1066 = FALSE; break; }
		$_1066 = TRUE; break;
	}
	while(0);
	if( $_1066 === TRUE ) { return $this->finalise($result); }
	if( $_1066 === FALSE) { return FALSE; }
}

public function enum_value__construct ( &$self ) {
		$self['node'] = 'constant';
		$self['type'] = 'enumeration_value';
	}

public function enum_value_enum_type_name ( &$self, $sub ) {
		$self['enumeration'] = $sub['text'] ;
	}

public function enum_value_enum_type_value ( &$self, $sub ) {
		$self['value'] = $sub['text'] ;
	}

/* enum-constant: enum-type-name "." enum-type-value */
protected $match_enum_constant_typestack = array('enum_constant');
function match_enum_constant ($stack = array()) {
	$matchrule = "enum_constant"; $result = $this->construct($matchrule, $matchrule, null);
	$_1071 = NULL;
	do {
		$matcher = 'match_'.'enum_type_name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1071 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '.') {
			$this->pos += 1;
			$result["text"] .= '.';
		}
		else { $_1071 = FALSE; break; }
		$matcher = 'match_'.'enum_type_value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1071 = FALSE; break; }
		$_1071 = TRUE; break;
	}
	while(0);
	if( $_1071 === TRUE ) { return $this->finalise($result); }
	if( $_1071 === FALSE) { return FALSE; }
}

public function enum_constant__construct ( &$self ) {
		$self['node'] = 'constant';
		$self['type'] = 'enumeration_value';
	}

public function enum_constant_enum_type_name ( &$self, $sub ) {
		$self['enumeration'] = $sub['text'] ;
	}

public function enum_constant_enum_type_value ( &$self, $sub ) {
		$self['value'] = $sub['text'] ;
	}

/* enumeration-type-value-literal: enum-type-name "[" enum-type-value "]" */
protected $match_enumeration_type_value_literal_typestack = array('enumeration_type_value_literal');
function match_enumeration_type_value_literal ($stack = array()) {
	$matchrule = "enumeration_type_value_literal"; $result = $this->construct($matchrule, $matchrule, null);
	$_1077 = NULL;
	do {
		$matcher = 'match_'.'enum_type_name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1077 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_1077 = FALSE; break; }
		$matcher = 'match_'.'enum_type_value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1077 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_1077 = FALSE; break; }
		$_1077 = TRUE; break;
	}
	while(0);
	if( $_1077 === TRUE ) { return $this->finalise($result); }
	if( $_1077 === FALSE) { return FALSE; }
}

public function enumeration_type_value_literal_enum_type_name ( &$self, $sub ) {
		$self['enum_name'] = $sub['text'] ;
	}

public function enumeration_type_value_literal_enum_type_value ( &$self, $sub ) {
		$self['enum_value'] = $sub['text'] ;
	}

/* named-type: /[A-Z][a-zA-Z0-9]{0,}/ */
protected $match_named_type_typestack = array('named_type');
function match_named_type ($stack = array()) {
	$matchrule = "named_type"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[A-Z][a-zA-Z0-9]{0,}/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* type-literal: (simple-type | integer-type | real-type | string-type | array-type | map-type | type-type | mutable-type | enumeration-type-value-literal | named-type) */
protected $match_type_literal_typestack = array('type_literal');
function match_type_literal ($stack = array()) {
	$matchrule = "type_literal"; $result = $this->construct($matchrule, $matchrule, null);
	$_1117 = NULL;
	do {
		$_1115 = NULL;
		do {
			$res_1080 = $result;
			$pos_1080 = $this->pos;
			$matcher = 'match_'.'simple_type'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1115 = TRUE; break;
			}
			$result = $res_1080;
			$this->pos = $pos_1080;
			$_1113 = NULL;
			do {
				$res_1082 = $result;
				$pos_1082 = $this->pos;
				$matcher = 'match_'.'integer_type'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_1113 = TRUE; break;
				}
				$result = $res_1082;
				$this->pos = $pos_1082;
				$_1111 = NULL;
				do {
					$res_1084 = $result;
					$pos_1084 = $this->pos;
					$matcher = 'match_'.'real_type'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_1111 = TRUE; break;
					}
					$result = $res_1084;
					$this->pos = $pos_1084;
					$_1109 = NULL;
					do {
						$res_1086 = $result;
						$pos_1086 = $this->pos;
						$matcher = 'match_'.'string_type'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_1109 = TRUE; break;
						}
						$result = $res_1086;
						$this->pos = $pos_1086;
						$_1107 = NULL;
						do {
							$res_1088 = $result;
							$pos_1088 = $this->pos;
							$matcher = 'match_'.'array_type'; $key = $matcher; $pos = $this->pos;
							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
							if ($subres !== FALSE) {
								$this->store( $result, $subres );
								$_1107 = TRUE; break;
							}
							$result = $res_1088;
							$this->pos = $pos_1088;
							$_1105 = NULL;
							do {
								$res_1090 = $result;
								$pos_1090 = $this->pos;
								$matcher = 'match_'.'map_type'; $key = $matcher; $pos = $this->pos;
								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
								if ($subres !== FALSE) {
									$this->store( $result, $subres );
									$_1105 = TRUE; break;
								}
								$result = $res_1090;
								$this->pos = $pos_1090;
								$_1103 = NULL;
								do {
									$res_1092 = $result;
									$pos_1092 = $this->pos;
									$matcher = 'match_'.'type_type'; $key = $matcher; $pos = $this->pos;
									$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
									if ($subres !== FALSE) {
										$this->store( $result, $subres );
										$_1103 = TRUE; break;
									}
									$result = $res_1092;
									$this->pos = $pos_1092;
									$_1101 = NULL;
									do {
										$res_1094 = $result;
										$pos_1094 = $this->pos;
										$matcher = 'match_'.'mutable_type'; $key = $matcher; $pos = $this->pos;
										$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
										if ($subres !== FALSE) {
											$this->store( $result, $subres );
											$_1101 = TRUE; break;
										}
										$result = $res_1094;
										$this->pos = $pos_1094;
										$_1099 = NULL;
										do {
											$res_1096 = $result;
											$pos_1096 = $this->pos;
											$matcher = 'match_'.'enumeration_type_value_literal'; $key = $matcher; $pos = $this->pos;
											$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
											if ($subres !== FALSE) {
												$this->store( $result, $subres );
												$_1099 = TRUE; break;
											}
											$result = $res_1096;
											$this->pos = $pos_1096;
											$matcher = 'match_'.'named_type'; $key = $matcher; $pos = $this->pos;
											$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
											if ($subres !== FALSE) {
												$this->store( $result, $subres );
												$_1099 = TRUE; break;
											}
											$result = $res_1096;
											$this->pos = $pos_1096;
											$_1099 = FALSE; break;
										}
										while(0);
										if( $_1099 === TRUE ) { $_1101 = TRUE; break; }
										$result = $res_1094;
										$this->pos = $pos_1094;
										$_1101 = FALSE; break;
									}
									while(0);
									if( $_1101 === TRUE ) { $_1103 = TRUE; break; }
									$result = $res_1092;
									$this->pos = $pos_1092;
									$_1103 = FALSE; break;
								}
								while(0);
								if( $_1103 === TRUE ) { $_1105 = TRUE; break; }
								$result = $res_1090;
								$this->pos = $pos_1090;
								$_1105 = FALSE; break;
							}
							while(0);
							if( $_1105 === TRUE ) { $_1107 = TRUE; break; }
							$result = $res_1088;
							$this->pos = $pos_1088;
							$_1107 = FALSE; break;
						}
						while(0);
						if( $_1107 === TRUE ) { $_1109 = TRUE; break; }
						$result = $res_1086;
						$this->pos = $pos_1086;
						$_1109 = FALSE; break;
					}
					while(0);
					if( $_1109 === TRUE ) { $_1111 = TRUE; break; }
					$result = $res_1084;
					$this->pos = $pos_1084;
					$_1111 = FALSE; break;
				}
				while(0);
				if( $_1111 === TRUE ) { $_1113 = TRUE; break; }
				$result = $res_1082;
				$this->pos = $pos_1082;
				$_1113 = FALSE; break;
			}
			while(0);
			if( $_1113 === TRUE ) { $_1115 = TRUE; break; }
			$result = $res_1080;
			$this->pos = $pos_1080;
			$_1115 = FALSE; break;
		}
		while(0);
		if( $_1115 === FALSE) { $_1117 = FALSE; break; }
		$_1117 = TRUE; break;
	}
	while(0);
	if( $_1117 === TRUE ) { return $this->finalise($result); }
	if( $_1117 === FALSE) { return FALSE; }
}

public function type_literal_simple_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = strtolower($sub['text']) ;
	}

public function type_literal_integer_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'integer' ;
		$self['range'] = $sub['range'] ;
	}

public function type_literal_real_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'real' ;
		$self['range'] = $sub['range'] ;
	}

public function type_literal_string_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'string' ;
		$self['length_range'] = $sub['length_range'] ;
	}

public function type_literal_array_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'array' ;
		$self['item_type'] = $sub['item_type'] ;
		$self['length_range'] = $sub['length_range'] ;
	}

public function type_literal_map_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'map' ;
		$self['item_type'] = $sub['item_type'] ;
		$self['length_range'] = $sub['length_range'] ;
	}

public function type_literal_type_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'type' ;
		$self['ref_type'] = $sub['ref_type'] ;
	}

public function type_literal_mutable_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'mutable' ;
		$self['ref_type'] = $sub['ref_type'] ;
	}

public function type_literal_type_name_literal ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function type_literal_enumeration_type_value_literal ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'enumeration_value_type' ;
		$self['enumeration'] = $sub['enum_name'] ;
		$self['value'] = $sub['enum_value'] ;
	}

public function type_literal_named_type ( &$self, $sub ) {
		$self['node'] = 'type_term' ;
		$self['type'] = 'type_name' ;
		$self['type_name'] = $sub['text'] ;
	}

/* literal: real-value | integer-value | boolean-value | null-value | string-value */
protected $match_literal_typestack = array('literal');
function match_literal ($stack = array()) {
	$matchrule = "literal"; $result = $this->construct($matchrule, $matchrule, null);
	$_1134 = NULL;
	do {
		$res_1119 = $result;
		$pos_1119 = $this->pos;
		$matcher = 'match_'.'real_value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_1134 = TRUE; break;
		}
		$result = $res_1119;
		$this->pos = $pos_1119;
		$_1132 = NULL;
		do {
			$res_1121 = $result;
			$pos_1121 = $this->pos;
			$matcher = 'match_'.'integer_value'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1132 = TRUE; break;
			}
			$result = $res_1121;
			$this->pos = $pos_1121;
			$_1130 = NULL;
			do {
				$res_1123 = $result;
				$pos_1123 = $this->pos;
				$matcher = 'match_'.'boolean_value'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_1130 = TRUE; break;
				}
				$result = $res_1123;
				$this->pos = $pos_1123;
				$_1128 = NULL;
				do {
					$res_1125 = $result;
					$pos_1125 = $this->pos;
					$matcher = 'match_'.'null_value'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_1128 = TRUE; break;
					}
					$result = $res_1125;
					$this->pos = $pos_1125;
					$matcher = 'match_'.'string_value'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_1128 = TRUE; break;
					}
					$result = $res_1125;
					$this->pos = $pos_1125;
					$_1128 = FALSE; break;
				}
				while(0);
				if( $_1128 === TRUE ) { $_1130 = TRUE; break; }
				$result = $res_1123;
				$this->pos = $pos_1123;
				$_1130 = FALSE; break;
			}
			while(0);
			if( $_1130 === TRUE ) { $_1132 = TRUE; break; }
			$result = $res_1121;
			$this->pos = $pos_1121;
			$_1132 = FALSE; break;
		}
		while(0);
		if( $_1132 === TRUE ) { $_1134 = TRUE; break; }
		$result = $res_1119;
		$this->pos = $pos_1119;
		$_1134 = FALSE; break;
	}
	while(0);
	if( $_1134 === TRUE ) { return $this->finalise($result); }
	if( $_1134 === FALSE) { return FALSE; }
}

public function literal_integer_value ( &$self, $sub ) {
		$self['node'] = 'literal' ;
		$self['type'] = 'integer' ;
		$self['value'] = (int)$sub['text'] ;
	}

public function literal_real_value ( &$self, $sub ) {
		$self['node'] = 'literal' ;
		$self['type'] = 'real' ;
		$self['value'] = (float)$sub['text'] ;
	}

public function literal_boolean_value ( &$self, $sub ) {
		$self['node'] = 'literal' ;
		$self['type'] = 'boolean' ;
		$self['value'] = $sub['text'] === 'true';
	}

public function literal_null_value ( &$self, $sub ) {
		$self['node'] = 'literal' ;
		$self['type'] = 'null' ;
	}

public function literal_string_value ( &$self, $sub ) {
		$self['node'] = 'literal' ;
		$self['type'] = 'string' ;
		$self['value'] = substr($sub['text'], 1, -1);
	}

/* non-empty-tuple-constant: "[" whitespace (constant whitespace "," whitespace)* constant whitespace "]" */
protected $match_non_empty_tuple_constant_typestack = array('non_empty_tuple_constant');
function match_non_empty_tuple_constant ($stack = array()) {
	$matchrule = "non_empty_tuple_constant"; $result = $this->construct($matchrule, $matchrule, null);
	$_1147 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_1147 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1147 = FALSE; break; }
		while (true) {
			$res_1143 = $result;
			$pos_1143 = $this->pos;
			$_1142 = NULL;
			do {
				$matcher = 'match_'.'constant'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_1142 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_1142 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_1142 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_1142 = FALSE; break; }
				$_1142 = TRUE; break;
			}
			while(0);
			if( $_1142 === FALSE) {
				$result = $res_1143;
				$this->pos = $pos_1143;
				unset( $res_1143 );
				unset( $pos_1143 );
				break;
			}
		}
		$matcher = 'match_'.'constant'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1147 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1147 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_1147 = FALSE; break; }
		$_1147 = TRUE; break;
	}
	while(0);
	if( $_1147 === TRUE ) { return $this->finalise($result); }
	if( $_1147 === FALSE) { return FALSE; }
}

public function non_empty_tuple_constant__construct ( &$self ) {
		$self['items'] = array();
	}

public function non_empty_tuple_constant_constant ( &$self, $sub ) {
		$self['items'][] = $sub;
	}

/* tuple-constant: "[]" | non-empty-tuple-constant */
protected $match_tuple_constant_typestack = array('tuple_constant');
function match_tuple_constant ($stack = array()) {
	$matchrule = "tuple_constant"; $result = $this->construct($matchrule, $matchrule, null);
	$_1152 = NULL;
	do {
		$res_1149 = $result;
		$pos_1149 = $this->pos;
		if (( $subres = $this->literal( '[]' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_1152 = TRUE; break;
		}
		$result = $res_1149;
		$this->pos = $pos_1149;
		$matcher = 'match_'.'non_empty_tuple_constant'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_1152 = TRUE; break;
		}
		$result = $res_1149;
		$this->pos = $pos_1149;
		$_1152 = FALSE; break;
	}
	while(0);
	if( $_1152 === TRUE ) { return $this->finalise($result); }
	if( $_1152 === FALSE) { return FALSE; }
}

public function tuple_constant__construct ( &$self ) {
		$self['node'] = 'constant';
		$self['type'] = 'tuple';
		$self['items'] = array();
	}

public function tuple_constant_non_empty_tuple_constant ( &$self, $sub ) {
		$self['items'] = $sub['items'];
	}

/* record-key-constant-pair: record-key whitespace ":" whitespace constant */
protected $match_record_key_constant_pair_typestack = array('record_key_constant_pair');
function match_record_key_constant_pair ($stack = array()) {
	$matchrule = "record_key_constant_pair"; $result = $this->construct($matchrule, $matchrule, null);
	$_1159 = NULL;
	do {
		$matcher = 'match_'.'record_key'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1159 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1159 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_1159 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1159 = FALSE; break; }
		$matcher = 'match_'.'constant'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1159 = FALSE; break; }
		$_1159 = TRUE; break;
	}
	while(0);
	if( $_1159 === TRUE ) { return $this->finalise($result); }
	if( $_1159 === FALSE) { return FALSE; }
}

public function record_key_constant_pair_record_key ( &$self, $sub ) {
		$self['key'] = $sub['text'];
	}

public function record_key_constant_pair_constant ( &$self, $sub ) {
		$self['value'] = $sub;
	}

/* non-empty-record-constant: "[" whitespace (record-key-constant-pair whitespace "," whitespace)* record-key-constant-pair whitespace "]" */
protected $match_non_empty_record_constant_typestack = array('non_empty_record_constant');
function match_non_empty_record_constant ($stack = array()) {
	$matchrule = "non_empty_record_constant"; $result = $this->construct($matchrule, $matchrule, null);
	$_1172 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_1172 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1172 = FALSE; break; }
		while (true) {
			$res_1168 = $result;
			$pos_1168 = $this->pos;
			$_1167 = NULL;
			do {
				$matcher = 'match_'.'record_key_constant_pair'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_1167 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_1167 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_1167 = FALSE; break; }
				$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_1167 = FALSE; break; }
				$_1167 = TRUE; break;
			}
			while(0);
			if( $_1167 === FALSE) {
				$result = $res_1168;
				$this->pos = $pos_1168;
				unset( $res_1168 );
				unset( $pos_1168 );
				break;
			}
		}
		$matcher = 'match_'.'record_key_constant_pair'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1172 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1172 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_1172 = FALSE; break; }
		$_1172 = TRUE; break;
	}
	while(0);
	if( $_1172 === TRUE ) { return $this->finalise($result); }
	if( $_1172 === FALSE) { return FALSE; }
}

public function non_empty_record_constant__construct ( &$self ) {
		$self['items'] = array();
	}

public function non_empty_record_constant_record_key_constant_pair ( &$self, $sub ) {
		$self['items'][$sub['key']] = $sub['value'];
	}

/* record-constant: "[:]" | non-empty-record-constant */
protected $match_record_constant_typestack = array('record_constant');
function match_record_constant ($stack = array()) {
	$matchrule = "record_constant"; $result = $this->construct($matchrule, $matchrule, null);
	$_1177 = NULL;
	do {
		$res_1174 = $result;
		$pos_1174 = $this->pos;
		if (( $subres = $this->literal( '[:]' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_1177 = TRUE; break;
		}
		$result = $res_1174;
		$this->pos = $pos_1174;
		$matcher = 'match_'.'non_empty_record_constant'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_1177 = TRUE; break;
		}
		$result = $res_1174;
		$this->pos = $pos_1174;
		$_1177 = FALSE; break;
	}
	while(0);
	if( $_1177 === TRUE ) { return $this->finalise($result); }
	if( $_1177 === FALSE) { return FALSE; }
}

public function record_constant__construct ( &$self ) {
		$self['node'] = 'constant';
		$self['type'] = 'record';
		$self['items'] = array();
	}

public function record_constant_non_empty_record_constant ( &$self, $sub ) {
		$self['items'] = $sub['items'];
	}

/* literal-constant: literal */
protected $match_literal_constant_typestack = array('literal_constant');
function match_literal_constant ($stack = array()) {
	$matchrule = "literal_constant"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'literal'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres );
		return $this->finalise($result);
	}
	else { return FALSE; }
}

public function literal_constant__construct ( &$self ) {
		$self['node'] = 'constant';
		$self['type'] = 'literal';
	}

public function literal_constant_literal ( &$self, $sub ) {
		$self['literal'] = $sub;
	}

/* constant: tuple-constant | record-constant | function-constant | enum-constant | type-constant | literal-constant */
protected $match_constant_typestack = array('constant');
function match_constant ($stack = array()) {
	$matchrule = "constant"; $result = $this->construct($matchrule, $matchrule, null);
	$_1199 = NULL;
	do {
		$res_1180 = $result;
		$pos_1180 = $this->pos;
		$matcher = 'match_'.'tuple_constant'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_1199 = TRUE; break;
		}
		$result = $res_1180;
		$this->pos = $pos_1180;
		$_1197 = NULL;
		do {
			$res_1182 = $result;
			$pos_1182 = $this->pos;
			$matcher = 'match_'.'record_constant'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1197 = TRUE; break;
			}
			$result = $res_1182;
			$this->pos = $pos_1182;
			$_1195 = NULL;
			do {
				$res_1184 = $result;
				$pos_1184 = $this->pos;
				$matcher = 'match_'.'function_constant'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_1195 = TRUE; break;
				}
				$result = $res_1184;
				$this->pos = $pos_1184;
				$_1193 = NULL;
				do {
					$res_1186 = $result;
					$pos_1186 = $this->pos;
					$matcher = 'match_'.'enum_constant'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_1193 = TRUE; break;
					}
					$result = $res_1186;
					$this->pos = $pos_1186;
					$_1191 = NULL;
					do {
						$res_1188 = $result;
						$pos_1188 = $this->pos;
						$matcher = 'match_'.'type_constant'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_1191 = TRUE; break;
						}
						$result = $res_1188;
						$this->pos = $pos_1188;
						$matcher = 'match_'.'literal_constant'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_1191 = TRUE; break;
						}
						$result = $res_1188;
						$this->pos = $pos_1188;
						$_1191 = FALSE; break;
					}
					while(0);
					if( $_1191 === TRUE ) { $_1193 = TRUE; break; }
					$result = $res_1186;
					$this->pos = $pos_1186;
					$_1193 = FALSE; break;
				}
				while(0);
				if( $_1193 === TRUE ) { $_1195 = TRUE; break; }
				$result = $res_1184;
				$this->pos = $pos_1184;
				$_1195 = FALSE; break;
			}
			while(0);
			if( $_1195 === TRUE ) { $_1197 = TRUE; break; }
			$result = $res_1182;
			$this->pos = $pos_1182;
			$_1197 = FALSE; break;
		}
		while(0);
		if( $_1197 === TRUE ) { $_1199 = TRUE; break; }
		$result = $res_1180;
		$this->pos = $pos_1180;
		$_1199 = FALSE; break;
	}
	while(0);
	if( $_1199 === TRUE ) { return $this->finalise($result); }
	if( $_1199 === FALSE) { return FALSE; }
}

public function constant_tuple_constant ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function constant_record_constant ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function constant_function_constant ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function constant_type_constant ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function constant_literal_constant ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function constant_enum_constant ( &$self, $sub ) {
		$self['node'] = 'constant' ;
		$self['type'] = 'enumeration_value' ;
		$self['enumeration'] = $sub['enumeration'] ;
		$self['value'] = $sub['value'] ;
	}

/* constant-assignment-expression: variable-name whitespace "=" whitespace constant */
protected $match_constant_assignment_expression_typestack = array('constant_assignment_expression');
function match_constant_assignment_expression ($stack = array()) {
	$matchrule = "constant_assignment_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_1206 = NULL;
	do {
		$matcher = 'match_'.'variable_name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1206 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1206 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
		}
		else { $_1206 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1206 = FALSE; break; }
		$matcher = 'match_'.'constant'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1206 = FALSE; break; }
		$_1206 = TRUE; break;
	}
	while(0);
	if( $_1206 === TRUE ) { return $this->finalise($result); }
	if( $_1206 === FALSE) { return FALSE; }
}

public function constant_assignment_expression__construct ( &$self ) {
		$self['node'] = 'module_expression';
		$self['expression_type'] = 'constant_assignment';
	}

public function constant_assignment_expression_variable_name ( &$self, $sub ) {
		$self['variable_name'] = $sub['text'] ;
	}

public function constant_assignment_expression_constant ( &$self, $sub ) {
		$self['value'] = $sub;
	}

/* type-cast-expression: whitespace named-type whitespace "==>" whitespace named-type whitespace (error-type whitespace)? "::" whitespace function-body */
protected $match_type_cast_expression_typestack = array('type_cast_expression');
function match_type_cast_expression ($stack = array()) {
	$matchrule = "type_cast_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_1222 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1222 = FALSE; break; }
		$matcher = 'match_'.'named_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1222 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1222 = FALSE; break; }
		if (( $subres = $this->literal( '==>' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_1222 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1222 = FALSE; break; }
		$matcher = 'match_'.'named_type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1222 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1222 = FALSE; break; }
		$res_1218 = $result;
		$pos_1218 = $this->pos;
		$_1217 = NULL;
		do {
			$matcher = 'match_'.'error_type'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_1217 = FALSE; break; }
			$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_1217 = FALSE; break; }
			$_1217 = TRUE; break;
		}
		while(0);
		if( $_1217 === FALSE) {
			$result = $res_1218;
			$this->pos = $pos_1218;
			unset( $res_1218 );
			unset( $pos_1218 );
		}
		if (( $subres = $this->literal( '::' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_1222 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1222 = FALSE; break; }
		$matcher = 'match_'.'function_body'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1222 = FALSE; break; }
		$_1222 = TRUE; break;
	}
	while(0);
	if( $_1222 === TRUE ) { return $this->finalise($result); }
	if( $_1222 === FALSE) { return FALSE; }
}

public function type_cast_expression__construct ( &$self ) {
		$self['node'] = 'module_expression';
		$self['expression_type'] = 'type_cast';
		$self['any_error'] = false;
		$self['error_type'] = [
			'node' => 'type_term',
			'type' => 'nothing'
		];
		$self['types'] = [];
	}

public function type_cast_expression_named_type ( &$self, $sub ) {
		$self['types'][] = [
			'node' => 'type_term',
			'type' => 'type_name',
			'type_name' => $sub['text']
		];
	}

public function type_cast_expression_function_body ( &$self, $sub ) {
		$self['function_body'] = $sub ;
	}

public function type_cast_expression_error_type ( &$self, $sub ) {
		$self['any_error'] = $sub['any_error'] ;
		$self['error_type'] = $sub['error_type'] ;
	}

/* module-expression: constant-assignment-expression | type-definition-expression | type-cast-expression */
protected $match_module_expression_typestack = array('module_expression');
function match_module_expression ($stack = array()) {
	$matchrule = "module_expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_1231 = NULL;
	do {
		$res_1224 = $result;
		$pos_1224 = $this->pos;
		$matcher = 'match_'.'constant_assignment_expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_1231 = TRUE; break;
		}
		$result = $res_1224;
		$this->pos = $pos_1224;
		$_1229 = NULL;
		do {
			$res_1226 = $result;
			$pos_1226 = $this->pos;
			$matcher = 'match_'.'type_definition_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1229 = TRUE; break;
			}
			$result = $res_1226;
			$this->pos = $pos_1226;
			$matcher = 'match_'.'type_cast_expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_1229 = TRUE; break;
			}
			$result = $res_1226;
			$this->pos = $pos_1226;
			$_1229 = FALSE; break;
		}
		while(0);
		if( $_1229 === TRUE ) { $_1231 = TRUE; break; }
		$result = $res_1224;
		$this->pos = $pos_1224;
		$_1231 = FALSE; break;
	}
	while(0);
	if( $_1231 === TRUE ) { return $this->finalise($result); }
	if( $_1231 === FALSE) { return FALSE; }
}

public function module_expression_type_definition_expression ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function module_expression_constant_assignment_expression ( &$self, $sub ) {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}

public function module_expression_type_cast_expression ( &$self, $sub ) {
		$self['node'] = 'module_expression';
		$self['expression_type'] = 'type_cast';
		$self['cast'] = [
			'from_type' => $sub['types'][0],
			'to_type' => $sub['types'][1],
			'function_body' => $sub['function_body'],
			'any_error' => $sub['any_error'],
			'error_type' => $sub['error_type']
		] ;
	}

/* module-comment: "//" */
protected $match_module_comment_typestack = array('module_comment');
function match_module_comment ($stack = array()) {
	$matchrule = "module_comment"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->literal( '//' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* module-expressions: whitespace ( (module-expression whitespace ";" whitespace) | module-comment)* */
protected $match_module_expressions_typestack = array('module_expressions');
function match_module_expressions ($stack = array()) {
	$matchrule = "module_expressions"; $result = $this->construct($matchrule, $matchrule, null);
	$_1247 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1247 = FALSE; break; }
		while (true) {
			$res_1246 = $result;
			$pos_1246 = $this->pos;
			$_1245 = NULL;
			do {
				$_1243 = NULL;
				do {
					$res_1235 = $result;
					$pos_1235 = $this->pos;
					$_1240 = NULL;
					do {
						$matcher = 'match_'.'module_expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
						}
						else { $_1240 = FALSE; break; }
						$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
						}
						else { $_1240 = FALSE; break; }
						if (substr($this->string,$this->pos,1) == ';') {
							$this->pos += 1;
							$result["text"] .= ';';
						}
						else { $_1240 = FALSE; break; }
						$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
						}
						else { $_1240 = FALSE; break; }
						$_1240 = TRUE; break;
					}
					while(0);
					if( $_1240 === TRUE ) { $_1243 = TRUE; break; }
					$result = $res_1235;
					$this->pos = $pos_1235;
					$matcher = 'match_'.'module_comment'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_1243 = TRUE; break;
					}
					$result = $res_1235;
					$this->pos = $pos_1235;
					$_1243 = FALSE; break;
				}
				while(0);
				if( $_1243 === FALSE) { $_1245 = FALSE; break; }
				$_1245 = TRUE; break;
			}
			while(0);
			if( $_1245 === FALSE) {
				$result = $res_1246;
				$this->pos = $pos_1246;
				unset( $res_1246 );
				unset( $pos_1246 );
				break;
			}
		}
		$_1247 = TRUE; break;
	}
	while(0);
	if( $_1247 === TRUE ) { return $this->finalise($result); }
	if( $_1247 === FALSE) { return FALSE; }
}

public function module_expressions__construct ( &$self ) {
		$self['elements'] = array() ;
	}

public function module_expressions_module_expression ( &$self, $sub ) {
		$self['elements'][] = $sub ;
	}

public function module_expressions_module_comment ( &$self, $sub ) {
		$self['elements'][] = [
			'node' => 'comment'
		];
	}

/* module-name: whitespace (module-name-node "\\")* module-name-node whitespace ";" */
protected $match_module_name_typestack = array('module_name');
function match_module_name ($stack = array()) {
	$matchrule = "module_name"; $result = $this->construct($matchrule, $matchrule, null);
	$_1257 = NULL;
	do {
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1257 = FALSE; break; }
		while (true) {
			$res_1253 = $result;
			$pos_1253 = $this->pos;
			$_1252 = NULL;
			do {
				$matcher = 'match_'.'module_name_node'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_1252 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == '\\') {
					$this->pos += 1;
					$result["text"] .= '\\';
				}
				else { $_1252 = FALSE; break; }
				$_1252 = TRUE; break;
			}
			while(0);
			if( $_1252 === FALSE) {
				$result = $res_1253;
				$this->pos = $pos_1253;
				unset( $res_1253 );
				unset( $pos_1253 );
				break;
			}
		}
		$matcher = 'match_'.'module_name_node'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1257 = FALSE; break; }
		$matcher = 'match_'.'whitespace'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1257 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ';') {
			$this->pos += 1;
			$result["text"] .= ';';
		}
		else { $_1257 = FALSE; break; }
		$_1257 = TRUE; break;
	}
	while(0);
	if( $_1257 === TRUE ) { return $this->finalise($result); }
	if( $_1257 === FALSE) { return FALSE; }
}

public function module_name__construct ( &$self ) {
		$self['module_name'] = array() ;
	}

public function module_name_module_name_node ( &$self, $sub ) {
		$self['module_name'][] = $sub['text'] ;
	}

/* module: "->" module-name module-expressions */
protected $match_module_typestack = array('module');
function match_module ($stack = array()) {
	$matchrule = "module"; $result = $this->construct($matchrule, $matchrule, null);
	$_1262 = NULL;
	do {
		if (( $subres = $this->literal( '->' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_1262 = FALSE; break; }
		$matcher = 'match_'.'module_name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1262 = FALSE; break; }
		$matcher = 'match_'.'module_expressions'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_1262 = FALSE; break; }
		$_1262 = TRUE; break;
	}
	while(0);
	if( $_1262 === TRUE ) { return $this->finalise($result); }
	if( $_1262 === FALSE) { return FALSE; }
}

public function module__construct ( &$self ) {
		$self['node'] = 'module' ;
	}

public function module_module_name ( &$self, $sub ) {
		$self['module_name'] = implode('\\', $sub['module_name']) ;
	}

public function module_module_expressions ( &$self, $sub ) {
		$self['elements'] = $sub['elements'];
	}



	private function copy(&$self, $sub): void {
		foreach($sub as $k => $v) {
			if (!array_key_exists($k, $self)) {
				$self[$k] = $v ;
			}
		}
	}
}

//ini_set('memory_limit', '25M');
//set_time_limit(2);
//header('Content-type: text/plain');
//$p = new Cast(file_get_contents(__DIR__ . '/start.cast')) ;
//echo json_encode( $p->match_expression(), JSON_PRETTY_PRINT ) ;

//$p = new Cast(file_get_contents(__DIR__ . '/program.cast')) ;
//echo json_encode( $p->match_module(), JSON_PRETTY_PRINT ) ;

//$p = new Cast(file_get_contents(__DIR__ . '/product.cast')) ;
//$p = new Cast(file_get_contents(__DIR__ . '/test.cast')) ;
//echo json_encode( $p->match_module(), JSON_PRETTY_PRINT ) ;
//echo json_encode( $p->match_sequence_expression(), JSON_PRETTY_PRINT ) ;