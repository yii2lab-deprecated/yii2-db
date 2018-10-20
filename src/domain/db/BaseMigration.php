<?php

namespace yii2lab\db\domain\db;

use Yii;
use yii\db\Migration;

/**
 * Migration
 */
class BaseMigration extends Migration {
	
	protected $tableOptions;
	protected $table;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		$this->initTableName();
	}
	
	public function enum($items) {
		if(is_array($items)) {
			$items = "'" . implode("', '", $items) . "'";
		}
		return "ENUM({$items})";
	}
	
	private function initTableName() {
		if(!empty($this->table)) {
			return;
		}
		$className = basename(get_class($this));
		$classNameArr = explode('_', $className);
		$classNameArrStriped = array_slice($classNameArr, 3, -1);
		$this->table = implode('_', $classNameArrStriped);
	}
	
	protected function pureTableName($table = null) {
		if(empty($table)) {
			$table = $this->table;
		}
		$table = str_replace(['{', '}', '%'], '', $table);
		return $table;
	}
	
	protected function generateNameForKey($type, $name, $data = null) {
		return $type . '-' . $name . '-' . hash('crc32b', serialize($data));
	}
	
	protected function myAddForeignKey($columns, $refTable, $refColumns, $delete = 'CASCADE', $update = 'CASCADE') {
		if(Yii::$app->db->driverName == 'sqlite') {
			return null;
		}
		$name = $this->generateNameForKey('fk', $this->pureTableName(), [$columns, $refTable, $refColumns]);
		return $this->addForeignKey($name, $this->table, $columns, $refTable, $refColumns, $delete, $update);
	}
	
	protected function myCreateIndex($columns, $unique = false) {
		$columns = is_array($columns) ? $columns : [$columns];
		$type = $unique ? 'uni' : 'idx';
		$name = $this->generateNameForKey($type, $this->pureTableName(), $columns);
		return parent::createIndex($name, $this->table, $columns, $unique);
	}
	
	protected function myAddPrimaryKey($columns) {
		$columns = is_array($columns) ? $columns : [$columns];
		$name = $this->generateNameForKey('pk', $this->pureTableName(), $columns);
		return parent::addPrimaryKey($name, $this->table, $columns);
	}
	
	protected function myCreateIndexUnique($columns) {
		return $this->myCreateIndex($columns, true);
	}
}
