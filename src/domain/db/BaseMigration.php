<?php

namespace yii2lab\db\domain\db;

use yii\db\Migration;
use yii2lab\db\domain\helpers\MigrationHelper;
use yii2lab\db\domain\traits\FieldTypeTrait;
use yii2lab\db\domain\traits\MyMethodTrait;

abstract class BaseMigration extends Migration {
	
	use FieldTypeTrait;
	use MyMethodTrait;
	
	protected $table;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		$this->initTableName();
	}
	
	private function initTableName() {
		if(!empty($this->table)) {
			return;
		}
		$this->table = MigrationHelper::getTableNameOfClass(get_class($this));
	}
	
}
