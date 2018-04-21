<?php

namespace yii2lab\db\domain\behaviors\enum;

class Format
{
 
	public static function encode($array) {
		return '{' . implode(',', $array);
	}
	
	public static function decode($string) {
		$string = trim($string, '{}');
		return explode(',', $string);
	}
	
}
