<?php

namespace yii2lab\db\domain\behaviors\serialize;

class Format
{
 
	public static function encode($array) {
		if(empty($array)) {
			return serialize([]);
		}
		return serialize($array);
	}
	
	public static function decode($data) {
		if(is_array($data)) {
			return $data;
		}
		$result = unserialize($data);
		if(empty($result)) {
			return [];
		}
		return $result;
	}
	
}
