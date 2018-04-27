<?php

namespace yii2lab\db\domain\behaviors\enum;

use yii\helpers\ArrayHelper;

class Format
{
 
	public static function encode($array) {
		$string = implode(',', $array);
		return '{' . $string . '}';
	}
	
	public static function decode($data) {
		if(is_object($data)) {
			$data = ArrayHelper::toArray($data);
		}
		if(is_array($data)) {
			return $data;
		}
		if(is_string($data)) {
			return self::stringToArray($data);
		}
		return [];
	}
	
	private static function stringToArray($string) {
		$string = trim($string, '{}');
		$array = explode(',', $string);
		$array = is_array($array) ? $array : [];
		return $array;
	}
	
}
