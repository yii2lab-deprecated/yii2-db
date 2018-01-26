<?php

namespace yii2lab\db\domain\helpers;

use Yii;
use yii2lab\helpers\generator\ClassGeneratorHelper;

class MigrationHelper {
	
	public static function generateByTableName($tableName)
	{
		$tableSchema = Yii::$app->db->getTableSchema($tableName);
		$prefix = 'm' . gmdate('ymd_His');
		$dir = 'console\migrations';
		$className = $dir . '\\' . $prefix . "_create_{$tableName}_table";
		$config = [
			'className' => $className,
			'use' => ['yii2lab\migration\db\MigrationCreateTable as Migration'],
			'afterClassName' => 'extends Migration',
			'code' => self::getCode($tableName, $tableSchema),
			'namespace' => null,
		];
		ClassGeneratorHelper::generate($config);
		return $className;
	}
	
	private static function generateValueCode($columnData) {
		if($columnData->isPrimaryKey) {
			$columnCode = "\$this->primaryKey({$columnData->size})";
		} elseif($columnData->type == 'timestamp') {
			$columnCode = "\$this->timestamp()";
		} else {
			$columnCode = "\$this->{$columnData->phpType}({$columnData->size})";
		}
		if(empty($columnData->allowNull)) {
			$columnCode .= "->notNull()";
		}
		if(!empty($columnData->defaultValue)) {
			$columnCode .= "->defaultValue({$columnData->defaultValue})";
		}
		if(!empty($columnData->comment)) {
			$columnCode .= "->comment('{$columnData->comment}')";
		}
		return $columnCode;
	}
	
	private static function generateColumnsCode($columns) {
		$columnArr = [];
		foreach($columns as $columnName => $columnData) {
			$columnCode = self::generateValueCode($columnData);
			$columnArr[] = "'{$columnName}' => {$columnCode},";
		}
		$columnStr = implode("\n\t\t\t", $columnArr);
		return $columnStr;
	}
	
	private static function generateKeysCode($tableSchema) {
		$keysArr = [];
		
		foreach($tableSchema->foreignKeys as $foreign) {
			foreach($foreign as $kk => $rr) {
				if(!is_integer($kk)) {
					$keysArr[] =
						"\$this->myAddForeignKey(
			'{$kk}',
			'{{%{$foreign[0]}}}',
			'{$rr}',
			'CASCADE',
			'CASCADE'
		);";
				}
			}
		}
		
		$keysStr = implode("\n\t\t", $keysArr);
	}
	
	private static function getCode($tableName, $tableSchema) {
		$columnStr = self::generateColumnsCode($tableSchema->columns);
		$keysStr = self::generateKeysCode($tableSchema);
		$code = <<<CODE
	public \$table = '{{%{$tableName}}}';

	/**
	 * @inheritdoc
	 */
	public function getColumns()
	{
		return [
			{$columnStr}
		];
	}

	public function afterCreate()
	{
		{$keysStr}
	}
CODE;
		return $code;
	}
	
}