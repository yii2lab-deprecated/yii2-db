<?php

namespace yii2lab\db\domain\db;

/**
 * Migration
 */
abstract class MigrationAddColumn extends BaseMigration
{
	
	abstract public function getColumns();
	
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$columns = $this->getColumns();
		foreach($columns as $columnName => $columnConfig) {
			$this->addColumn($this->table, $columnName, $columnConfig);
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$columns = $this->getColumns();
		foreach($columns as $columnName => $columnConfig) {
			$this->dropColumn($this->table, $columnName);
		}
	}
	
}
