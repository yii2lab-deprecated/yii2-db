<?php

namespace yii2lab\db\domain\behaviors\migrate;

use yii\base\Behavior;
use yii2lab\db\domain\enums\EventEnum;
use yii2lab\db\domain\events\TableEvent;
use yii2lab\db\domain\enums\DbDriverEnum;

class NormalizeTableOptionsFilter extends Behavior {
	
	public function events() {
		return [
			EventEnum::BEFORE_CREATE_TABLE => 'beforeCreateTable',
		];
	}
	
	public function beforeCreateTable(TableEvent $event) {
		$event->options = $this->normalizeTableOptions($event, $event->options);
	}
	
	private function normalizeTableOptions(TableEvent $event, $options) {
		if(!empty($options)) {
			return $options;
		}
		switch($event->sender->db->driverName) {
			case DbDriverEnum::MYSQL:
				$tableOptions = $this->getTableOptions();
				return $tableOptions;
				break;
			case DbDriverEnum::PGSQL:
				break;
		}
		return null;
	}
	
	private function getTableOptions($engine = 'InnoDB') {
		return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=' . $engine;
	}
}
