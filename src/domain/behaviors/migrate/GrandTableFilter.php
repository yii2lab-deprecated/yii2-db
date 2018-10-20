<?php

namespace yii2lab\db\domain\behaviors\migrate;

use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii2lab\db\domain\enums\EventEnum;
use yii2lab\db\domain\events\TableEvent;
use yii2lab\db\domain\helpers\PostgresHelper;
use yii2lab\db\domain\helpers\SqlHelper;

class GrandTableFilter extends Behavior {
	
	public $users;
	
	public function events() {
		return [
			EventEnum::BEFORE_CREATE_TABLE => 'beforeCreateTable',
			EventEnum::AFTER_CREATE_TABLE => 'afterCreateTable',
		];
	}
	
	public function beforeCreateTable(TableEvent $event) {
	
	}
	
	public function afterCreateTable(TableEvent $event) {
		$defaultSchema = PostgresHelper::getDefaultSchema();
		if(empty($defaultSchema)) {
			return;
		}
		if(empty($this->users)) {
			return;
		}
		$users = ArrayHelper::toArray($this->users);
		foreach($users as $user) {
			$sqlList = $this->generateSql($defaultSchema, $user);
			SqlHelper::execute($sqlList);
		}
	}
	
	private function generateSql($defaultSchema, $user) {
		if(empty($defaultSchema) || empty($user)) {
			return null;
		}
		return [
			'GRANT USAGE ON SCHEMA ' . $defaultSchema . ' TO ' . $user,
			'GRANT SELECT, UPDATE, DELETE, INSERT ON ALL TABLES IN SCHEMA ' . $defaultSchema . ' TO ' . $user,
			'GRANT USAGE ON ALL SEQUENCES IN SCHEMA ' . $defaultSchema . ' TO ' . $user,
		];
	}
	
}
