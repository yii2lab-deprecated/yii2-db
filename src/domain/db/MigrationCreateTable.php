<?php

namespace yii2lab\db\domain\db;

use yii\base\NotSupportedException;
use yii2lab\db\domain\behaviors\migrate\GrandTableFilter;
use yii2lab\db\domain\behaviors\migrate\NormalizeTableOptionsFilter;
use yii2lab\db\domain\enums\EventEnum;
use yii2lab\db\domain\events\TableEvent;

abstract class MigrationCreateTable extends BaseMigration {
	
	abstract public function getColumns();
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		$this->attachBehaviors([
			GrandTableFilter::class,
			NormalizeTableOptionsFilter::class,
			//TableCommentFilter::class,
		]);
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
		$event = new TableEvent();
		$event->table = $this->table;
		
		$this->trigger(EventEnum::BEFORE_DROP_TABLE, $event);
		
		if(method_exists($this, 'beforeDrop')) {
			$this->beforeDrop();
		}
		$result = parent::dropTable($event->table);
		if(method_exists($this, 'afterDrop')) {
			$this->afterDrop();
		}
		
		$this->trigger(EventEnum::AFTER_DROP_TABLE, $event);
		
		return $result;
	}
	
	protected function myCreateTable($columns, $options = null) {
		$event = new TableEvent();
		$event->table = $this->table;
		$event->options = $options;
		
		$this->trigger(EventEnum::BEFORE_CREATE_TABLE, $event);
		
		if(method_exists($this, 'beforeCreate')) {
			$this->beforeCreate();
		}
		
		$tableSchema = $this->db->schema->getTableSchema($event->table);
		if($tableSchema === null) {
			parent::createTable($event->table, $columns, $event->options);
		}
		
		if(method_exists($this, 'afterCreate')) {
			$this->afterCreate();
		}
		
		$this->trigger(EventEnum::AFTER_CREATE_TABLE, $event);
	}
	
}
