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
		$this->myCreateTable($this->getColumns());
	}
	
	public function down()
	{
		$this->myDropTable();
	}

}
