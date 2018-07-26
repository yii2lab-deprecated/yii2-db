<?php

namespace yii2lab\db\domain\db;

/**
 * Migration
 */
abstract class MigrationCreateTable extends BaseMigration
{
	
	abstract public function getColumns();
	
	public function up()
	{
		try {
			$this->myCreateTable($this->getColumns());
		} catch(\yii\base\NotSupportedException $e) {
		
		}
	}
	
	public function down()
	{
		$this->myDropTable();
	}

}
