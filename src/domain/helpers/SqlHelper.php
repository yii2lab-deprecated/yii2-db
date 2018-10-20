<?php

namespace yii2lab\db\domain\helpers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

class SqlHelper {
	
	public static function execute($sqlList, $dbComponent = 'db') {
		if(empty($sqlList)) {
			throw new InvalidArgumentException('Empty sqlList!');
		}
		$sqlList = ArrayHelper::toArray($sqlList);
		foreach($sqlList as $sql) {
			self::executeSqlItem($sql, $dbComponent);
		}
	}
	
	private static function executeSqlItem($sql, $dbComponent = 'db') {
		/** @var Connection $db */
		$db = Yii::$app->get($dbComponent);
		$db->createCommand($sql)->execute();
	}
	
}
