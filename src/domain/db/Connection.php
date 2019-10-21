<?php

namespace yii2lab\db\domain\db;

use yii\helpers\ArrayHelper;
use yii2lab\db\domain\helpers\DbHelper;

class Connection extends \yii\db\Connection
{
	
	public $charset = 'utf8';
	public $enableSchemaCache = YII_ENV_PROD;
	
	public function __construct(array $config = []) {
		$name = YII_ENV_TEST ? 'test' : 'main';
		$connectionFromEnv = DbHelper::getConfigFromEnv($name);
		if (isset($connectionFromEnv['port'])){
			unset($connectionFromEnv['port']);
		}
		$config = ArrayHelper::merge($connectionFromEnv, $config);
		parent::__construct($config);
	}
	
}
