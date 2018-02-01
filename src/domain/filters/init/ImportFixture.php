<?php

namespace yii2lab\db\domain\filters\init;

use Yii;
use yii\base\BaseObject;
use yii2lab\console\helpers\Output;
use yii2lab\db\domain\helpers\Fixtures;

class ImportFixture extends BaseObject
{
	
	public $tableList;
	
	public function run() {
		$fixtures = Yii::createObject(Fixtures::className());
		$tables = $fixtures->import($this->tableList);
		Output::items($tables, 'Imported tables');
	}
	
}
