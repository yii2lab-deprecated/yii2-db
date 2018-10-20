<?php

namespace yii2lab\db\domain\db;

use Yii;
use yii\base\NotSupportedException;
use yii2lab\app\domain\helpers\EnvService;
use yii2lab\db\domain\behaviors\migrate\GrandTableFilter;
use yii2lab\db\domain\enums\DbDriverEnum;
use yii2lab\db\domain\enums\EventEnum;
use yii2lab\db\domain\events\TableEvent;

abstract class MigrationCreateTable extends BaseMigration {
	
	abstract public function getColumns();
	
	public function behaviors() {
		$grantToUser = EnvService::getConnection('main.username');
		return [
			[
				'class' => GrandTableFilter::class,
				'users' => [
					$grantToUser,
				],
			],
		];
	}
	
	public function up() {
		try {
			$this->myCreateTable($this->getColumns());
		} catch(NotSupportedException $e) {
		
		}
	}
	
	public function down() {
		$this->myDropTable();
	}
	
	protected function myDropTable() {
		if(method_exists($this, 'beforeDrop')) {
			$this->beforeDrop();
		}
		$result = parent::dropTable($this->table);
		if(method_exists($this, 'afterDrop')) {
			$this->afterDrop();
		}
		return $result;
	}
	
	protected function myCreateTable($columns, $options = null) {
		if(method_exists($this, 'beforeCreate')) {
			$this->beforeCreate();
		}
		$options = $this->normalizeTableOptions($options);
		$tableSchema = Yii::$app->db->schema->getTableSchema($this->table);
		if($tableSchema === null) {
			parent::createTable($this->table, $columns, $options);
		}
		
		if(method_exists($this, 'afterCreate')) {
			if(!empty($this->comment)) {
				if(Yii::$app->db->driverName != DbDriverEnum::PGSQL) {
					$this->comment($this->comment);
				}
			}
			$this->afterCreate();
		}
		
		$event = new TableEvent();
		$event->table = $this->table;
		$event->options = $options;
		$this->trigger(EventEnum::AFTER_CREATE_TABLE, $event);
	}
	
	protected function normalizeTableOptions($options) {
		if(!empty($options)) {
			return $options;
		}
		switch(Yii::$app->db->driverName) {
			case DbDriverEnum::MYSQL:
				return $this->getTableOptions();
				break;
			case DbDriverEnum::PGSQL:
				break;
		}
		return null;
	}
	
	public function getTableOptions($engine = 'InnoDB') {
		return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=' . $engine;
	}
	
	public function comment($text) {
		$this->addCommentOnTable($this->table, $text);
	}
	
}
