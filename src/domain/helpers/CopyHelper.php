<?php

namespace yii2lab\db\domain\helpers;

use Yii;

class CopyHelper {
	
	public static function run($classList) {
		foreach($classList as $className) {
			self::runClass($className);
		}
	}
	
	private static function runClass($className) {
		$class = Yii::createObject($className);
		$class->run();
	}
	
}