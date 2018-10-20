<?php

namespace yii2lab\db\domain\filters\migrate;

use yii2lab\db\domain\helpers\PostgresHelper;
use yii2lab\db\domain\helpers\SqlHelper;
use yii2lab\extension\scenario\base\BaseScenario;

class GrandTableFilter extends BaseScenario {
	
	public $user;
	
	public function run() {
		$defaultSchema = PostgresHelper::getDefaultSchema();
		if(empty($defaultSchema)) {
			return;
		}
		$sqlList = $this->generateSql($defaultSchema, $this->user);
		SqlHelper::execute($sqlList);
	}
	
	private function generateSql($defaultSchema, $user) {
		return [
			'GRANT USAGE ON SCHEMA ' . $defaultSchema . ' TO ' . $user,
			'GRANT SELECT, UPDATE, DELETE, INSERT ON ALL TABLES IN SCHEMA ' . $defaultSchema . ' TO ' . $user,
			'GRANT USAGE ON ALL SEQUENCES IN SCHEMA ' . $defaultSchema . ' TO ' . $user,
		];
	}
	
}
