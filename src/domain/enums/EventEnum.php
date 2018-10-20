<?php

namespace yii2lab\db\domain\enums;

use yii2lab\extension\enum\base\BaseEnum;

class EventEnum extends BaseEnum {
	
	const BEFORE_METHOD = 'BEFORE_METHOD';
	const AFTER_METHOD = 'AFTER_METHOD';
	
	const BEFORE_CREATE_TABLE = 'BEFORE_CREATE_TABLE';
	const AFTER_CREATE_TABLE = 'AFTER_CREATE_TABLE';
	
}
