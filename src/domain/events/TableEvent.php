<?php

namespace yii2lab\db\domain\events;

use yii\base\Event;
use yii2lab\db\domain\db\MigrationCreateTable;

/**
 * @property MigrationCreateTable $sender
 */
class TableEvent extends Event {
	
	public $table;
	public $options;
	
}
