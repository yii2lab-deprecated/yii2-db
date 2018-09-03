<?php

namespace yii2lab\db\domain\behaviors\json;

use paulzi\jsonBehavior\JsonField;
use yii\db\ActiveRecord;

/**
 *  @property ActiveRecord $owner
 */
class JsonBehavior extends \paulzi\jsonBehavior\JsonBehavior
{
	
	protected function encode() {
		$columns = $this->owner::getTableSchema()->columns;
		foreach ($this->attributes as $attribute) {
			$field = $this->owner->getAttribute($attribute);
			if (!$field instanceof JsonField) {
				$field = new JsonField($field);
			}
			$type = $columns[$attribute]->dbType;
			if($type != 'jsonb') {
				$field = (string) $field;
			}
			$this->owner->setAttribute($attribute, $field ?: $this->emptyValue);
		}
	}

}
