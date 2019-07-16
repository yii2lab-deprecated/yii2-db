<?php

namespace yii2lab\db\console\controllers;

use yii\db\pgsql\Schema;
use yii2lab\extension\console\helpers\input\Enter;
use yii2lab\extension\console\helpers\Output;
use yii2lab\extension\console\base\Controller;
use yii2lab\db\domain\helpers\MigrationHelper;

/**
 * Migration tools
 */
class MigrationController extends Controller
{
	
	/**
	 * Generate migration with columns and foreign keys
	 */
	public function actionGenerate()
	{
		$tableName = Enter::display('Enter table name');
		$className = MigrationHelper::generateByTableName($tableName);
		Output::block($className, 'Migration created!');
	}
	/**
	 * Generate migration with columns and foreign keys
	 */
	public function actionGenerateAll()
	{
		$schema = \Yii::$app->db->getSchema();
		foreach ($schema->getTableSchemas() as $tableName => $tableSchema){
			$className = MigrationHelper::generateByTableName($tableSchema->name);
			Output::block($className, 'Migration created!');
		}
	}
}
