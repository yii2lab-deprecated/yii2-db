<?php

namespace yii2lab\db\domain\events;

use yii\base\Event;

class TableEvent extends Event {
	
	public $table;
	public $options;
	
}
