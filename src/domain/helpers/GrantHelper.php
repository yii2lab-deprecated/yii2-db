<?php

namespace yii2lab\db\domain\helpers;

use Yii;

class GrantHelper {

	public static function run($user) {
		$defaultSchema = self::getDefaultSchema();
		if(empty($defaultSchema)) {
			return;
		}
		$sqlList = self::generateSql($defaultSchema, $user);
		self::runSqlList($sqlList);
	}
	
	private static function generateSql($defaultSchema, $user) {
		return [
			'GRANT USAGE ON SCHEMA ' . $defaultSchema . ' TO ' . $user,
			'GRANT SELECT, UPDATE, DELETE, INSERT ON ALL TABLES IN SCHEMA ' . $defaultSchema . ' TO ' . $user,
			'GRANT USAGE ON ALL SEQUENCES IN SCHEMA ' . $defaultSchema . ' TO ' . $user,
		];
	}
	
	private static function runSqlList($sqlList) {
		foreach($sqlList as $sql) {
			Yii::$app->db->createCommand($sql)->execute();
		}
	}
	
	private static function getDefaultSchema() {
		$config = env('connection.main');
		if($config['driver'] != 'pgsql') {
			return null;
		}
		if(empty($config['defaultSchema'])) {
			return null;
		}
		return $config['defaultSchema'];
	}
	
}