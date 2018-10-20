<?php

namespace yii2lab\db\domain\traits;

use Yii;
use yii2lab\db\domain\helpers\MigrationHelper;

trait MyMethodTrait {
	
	protected function generateNameForKey($type, $name, $data = null) {
		return $type . '-' . $name . '-' . hash('crc32b', serialize($data));
	}
	
	protected function myAddForeignKey($columns, $refTable, $refColumns, $delete = 'CASCADE', $update = 'CASCADE') {
		if(Yii::$app->db->driverName == 'sqlite') {
			return null;
		}
		$name = $this->generateNameForKey('fk', MigrationHelper::pureTableName($this->table), [$columns, $refTable, $refColumns]);
		return $this->addForeignKey($name, $this->table, $columns, $refTable, $refColumns, $delete, $update);
	}
	
	protected function myCreateIndex($columns, $unique = false) {
		$columns = is_array($columns) ? $columns : [$columns];
		$type = $unique ? 'uni' : 'idx';
		$name = $this->generateNameForKey($type, MigrationHelper::pureTableName($this->table), $columns);
		return parent::createIndex($name, $this->table, $columns, $unique);
	}
	
	protected function myAddPrimaryKey($columns) {
		$columns = is_array($columns) ? $columns : [$columns];
		$name = $this->generateNameForKey('pk', MigrationHelper::pureTableName($this->table), $columns);
		return parent::addPrimaryKey($name, $this->table, $columns);
	}
	
	protected function myCreateIndexUnique($columns) {
		return $this->myCreateIndex($columns, true);
	}
	
}
