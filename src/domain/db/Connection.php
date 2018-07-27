<?php

namespace yii2lab\db\domain\db;

use yii\helpers\ArrayHelper;
use yii2lab\app\domain\helpers\Env;
use yii2lab\db\domain\helpers\DbHelper;
use yii2lab\misc\enums\TimeEnum;

class Connection extends \yii\db\Connection
{
	
	private static $defaultConfig = [
		//'class' => 'yii\db\Connection',
		'charset' => 'utf8',
		'enableSchemaCache' => false,
		'schemaCacheDuration' => TimeEnum::SECOND_PER_HOUR,
		'schemaCache' => 'cache',
	];
	
	public function __construct(array $config = []) {
		$config = $this->getConfig();
		parent::__construct($config);
	}
	
	public function getConfig($config = [], $name = null) {
		if(empty($name)) {
			$name = YII_ENV_TEST ? 'test' : 'main';
		}
		$config = ArrayHelper::merge(self::$defaultConfig, $config);
		$config = ArrayHelper::merge($config, Env::get('db' . DOT . $name));
		$config = DbHelper::schemaMap($config);
		unset($config['defaultSchema']);
		unset($config['driver']);
		unset($config['host']);
		unset($config['dbname']);
		$config['enableSchemaCache'] = isset($config['enableSchemaCache']) ? $config['enableSchemaCache'] : YII_ENV_PROD;
		return $config;
	}
}
