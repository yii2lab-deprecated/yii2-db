<?php

namespace yii2lab\db\domain\drivers\db;

use Yii;
use yii\db\Query;
use yii2lab\db\domain\interfaces\DriverInterface;

abstract class BaseDriver implements DriverInterface
{
	
	abstract protected function showTables();
	abstract protected function disableForeignKeyChecks($table);
	
	public function loadData($table)
	{
		return $this->createQuery()->from($table)->all();
	}

	public function saveData($table, $data)
	{
		if(!$this->isExistsTable($table)) {
			return false;
		}
		/* if(!empty(DbHelper::isHasDataTable($table))) {
			return false;
		} */
		$this->disableForeignKeyChecks($table);
		$this->clearTable($table);
		$this->insertDataInTable($table, $data);
		return true;
	}
	
	public function getNameList()
	{
		$list = $this->showTables();
		return $this->filterTableList($list);
	}

	protected function clearTable($table)
	{
		return $this->createSql()->truncateTable($table)->execute();
	}
	
	protected function insertRowInTable($table, $row)
	{
		return $this->createSql()->insert($table, $row)->execute();
	}
	
	protected function insertDataInTable($table, $data)
	{
		foreach($data as $row) {
			$this->insertRowInTable($table, $row);
		}
	}
	
	protected function isExistsTable($table)
	{
		$schema = Yii::$app->db->schema->getTableSchema($table);
		return !empty($schema);
	}
	
	protected function isHasDataTable($table)
	{
		$result = $this->createQuery()->select('COUNT(*) as count')->from($table)->one();
		return intval($result['count']) > 0;
	}
	
	protected function filterTableList($all)
	{
		$result = [];
		foreach($all as $table) {
			if(!$this->isNotExclude($table)) {
				continue;
			}
			if(!$this->isHasDataTable($table)) {
				continue;
			}
			$result[] = $table;
		}
		return $result;
	}
	
	protected function executeSql($sql = null, $command = 'execute')
	{
		return $this->createSql($sql)->$command();
	}
	
	protected function createSql($sql = null)
	{
		return Yii::$app->db->createCommand($sql);
	}
	
	protected function createQuery()
	{
		return new Query();
	}
	
	protected function isNotExclude($table)
	{
		$excludeList = param('fixture.exclude');
		return !in_array($table, $excludeList);
	}

}
