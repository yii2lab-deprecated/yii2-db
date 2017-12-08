<?php

namespace yii2lab\db\domain\filters;

use Yii;
use yii\base\BaseObject;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii2lab\console\helpers\Output;

abstract class CopyBase extends BaseObject implements BaseInterface
{
	
	public $fromTable;
	public $toTable;
	public $map;
	
	public function run() {
		$rows = $this->allRowsFromTable($this->fromTable);
		$this->batchInsertToNewTable($rows);
	}
	
	protected function insertRow($row) {
		$newRow = $this->mapRow($row);
		$this->insertRowToNewTable($newRow);
	}
	
	protected function mapRow($row) {
		if(empty($this->map)) {
			Output::block('Not configure parameter "map"');
			return [];
		}
		$newRow = [];
		foreach($this->map as $to => $from) {
			$newRow[$to] = $row[$from];
		}
		return $newRow;
	}
	
	protected function allRowsFromTable($table) {
		try {
			$query = new Query;
			$query->from($this->getTableName($table));
			$rows = $query->all();
			$rows = ArrayHelper::toArray($rows);
			return $rows;
		} catch(\yii\db\Exception $e) {
			Output::block('Not found table "'.$table.'"');
			return [];
		}
	}
	
	protected function batchInsertToNewTable($rows) {
		foreach($rows as $row) {
			$this->insertRow($row);
		}
	}
	
	protected function insertRowToNewTable($row) {
		try {
			Yii::$app
				->db
				->createCommand()
				->insert($this->getTableName($this->toTable), $row)
				->execute();
		} catch(\Exception $e) {
			Output::line($e->getMessage());
		}
	}
	
	private function getTableName($table) {
		$table = trim($table, '{}%');
		if(empty($table)) {
			Output::block('Not configure parameter "table"');
			exit;
		}
		return "{{%$table}}";
	}

}
