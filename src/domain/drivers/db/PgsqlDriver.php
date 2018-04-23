<?php

namespace yii2lab\db\domain\drivers\db;

use yii2lab\helpers\Helper;

class PgsqlDriver extends BaseDriver
{
	
	protected function disableForeignKeyChecks($table)
	{
		$this->executeSql("ALTER TABLE \"$table\" DISABLE TRIGGER ALL;");
	}
	
	protected function showTables()
	{
		$defaultSchema = Helper::getDbConfig('defaultSchema');
		$where = ['schemaname' => $defaultSchema, 'tableowner' => 'postgres'];
		$all = $this->createQuery()->from('pg_catalog.pg_tables')->where($where)->all();
		$result = [];
		foreach($all as $item) {
			$result[] = $item['tablename'];
		}
		return $result;
	}
	
	protected function clearTable($table) {
		// TRUNCATE TABLE "geo_country" RESTART IDENTITY CASCADE
		$this->executeSql("TRUNCATE TABLE \"$table\" RESTART IDENTITY CASCADE");
	}
}
