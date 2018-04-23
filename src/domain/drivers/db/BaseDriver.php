<?php

namespace yii2lab\db\domain\drivers\db;

use Yii;
use yii\db\Query;
use yii\db\Transaction;
use yii2lab\db\domain\interfaces\DbDriverInterface;

abstract class BaseDriver implements DbDriverInterface
{
	//  TRUNCATE TABLE "geo_country" RESTART IDENTITY CASCADE
	abstract protected function showTables();
	abstract public function disableForeignKeyChecks($table);
	
	/**
	 * @var Transaction
	 */
	private $transaction;
	
	public function beginTransaction()
	{
		$this->transaction = Yii::$app->db->beginTransaction();
		//return $this->executeSql('BEGIN');
	}
	
	public function commitTransaction()
	{
		$this->transaction->commit();
		//return $this->executeSql('COMMIT');
	}
	
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
		//$this->disableForeignKeyChecks($table);
		//$this->clearTable($table);
		$this->insertDataInTable($table, $data);
		return true;
	}
	
	public function getNameList()
	{
		$list = $this->showTables();
		return $this->filterTableList($list);
	}
	
	public function clearTable($table)
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
