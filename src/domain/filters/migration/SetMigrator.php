<?php

namespace yii2lab\db\domain\filters\migration;

//use common\enums\app\ApiVersionEnum;
use Yii;
use yii2lab\app\domain\enums\AppEnum;
use yii2lab\extension\scenario\base\BaseScenario;
use yii2lab\helpers\ModuleHelper;
use yii2lab\extension\yii\helpers\FileHelper;
use yii2mod\helpers\ArrayHelper;

class SetMigrator extends BaseScenario {

	public $path = [];
	public $scan = [];
	
	public function isEnabled() {
		return APP == CONSOLE;
	}
	
	public function run() {
		$config = $this->getData();
		prr('SetMigrator',1,1);
		$this->setData($config);
	}
	
}
