<?php

namespace yii2lab\db\domain\traits;

trait FieldTypeTrait {
	
	public function enum($items) {
		if(is_array($items)) {
			$items = "'" . implode("', '", $items) . "'";
		}
		return "ENUM({$items})";
	}
	
}
