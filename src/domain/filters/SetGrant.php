<?php

namespace yii2lab\db\domain\filters;

use Yii;
use yii\base\BaseObject;
use yii2lab\misc\interfaces\CommandInterface;

class SetGrant extends BaseObject implements CommandInterface
{
	
	public $grantUser;
	
	public function run() {
		$defaultSchema = $this->getDefaultSchema();
		if(empty($defaultSchema)) {
			return;
		}
		$sqlList = $this->generateSql($defaultSchema, $this->grantUser);
		$this->runSqlList($sqlList);
	}
	
	private function generateSql($defaultSchema, $user) {
		return [
			'GRANT USAGE ON SCHEMA ' . $defaultSchema . ' TO ' . $user,
			'GRANT SELECT, UPDATE, DELETE, INSERT ON ALL TABLES IN SCHEMA ' . $defaultSchema . ' TO ' . $user,
			'GRANT USAGE ON ALL SEQUENCES IN SCHEMA ' . $defaultSchema . ' TO ' . $user,
		];
	}
	
	private function runSqlList($sqlList) {
		foreach($sqlList as $sql) {
			Yii::$app->db->createCommand($sql)->execute();
		}
	}
	
	private function getDefaultSchema() {
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
