<?php

namespace yii2lab\db\domain\db;

use yii\helpers\ArrayHelper;
use yii2lab\app\domain\helpers\EnvService;
use yii2lab\misc\enums\DbDriverEnum;
use yii2lab\misc\enums\TimeEnum;

class Connection extends \yii\db\Connection
{
	
	private static $defaultConfig = [
		'charset' => 'utf8',
		'enableSchemaCache' => false,
		'schemaCacheDuration' => TimeEnum::SECOND_PER_HOUR,
		'schemaCache' => 'cache',
	];
	
	public function __construct(array $config = []) {
		$config = ArrayHelper::merge($this->getConfig(), $config);
		parent::__construct($config);
	}
	
	private function getConfig($config = [], $name = null) {
		if(empty($name)) {
			$name = YII_ENV_TEST ? 'test' : 'main';
		}
		$config = ArrayHelper::merge(self::$defaultConfig, $config);
		$config = ArrayHelper::merge($config, EnvService::getConnection($name));
		$config = $this->schemaMap($config);
		$config = $this->normalizeConfig($config);
		unset($config['defaultSchema']);
		unset($config['driver']);
		unset($config['host']);
		unset($config['dbname']);
		$config['enableSchemaCache'] = isset($config['enableSchemaCache']) ? $config['enableSchemaCache'] : YII_ENV_PROD;
		return $config;
	}
	
	private function normalizeConfig($db)
	{
		$db['password'] = isset($db['password']) ? $db['password'] : '';
		$db['tablePrefix'] = isset($db['tablePrefix']) ? $db['tablePrefix'] : '';
		if (empty($db['dsn'])) {
			if($db['driver'] == DbDriverEnum::SQLITE) {
				$db['dsn'] = $db['driver'] . ':' . $db['dbname'];
			} else {
				$db['host'] = isset($db['host']) ? $db['host'] : 'localhost';
				$db['dsn'] = $db['driver'] . ':host=' . $db['host'] . ';dbname=' . $db['dbname'];
			}
		}
		if($db['driver'] != DbDriverEnum::PGSQL && isset($db['defaultSchema'])) {
			unset($db['defaultSchema']);
		}
		return $db;
	}
	
	private function schemaMap($config) {
		if($config['driver'] != DbDriverEnum::PGSQL) {
			unset($config['schemaMap']);
			return $config;
		}
		if(!empty($config['schemaMap'])) {
			$config = $this->postgresFix($config, $config['schemaMap']);
		}
		if($config['driver'] == 'pgsql') {
			$schemaMap =  [
				'pgsql' => [
					'class' => 'yii\db\pgsql\Schema',
					'defaultSchema' => $config['defaultSchema'],
				]
			];
			$config = $this->postgresFix($config, $schemaMap);
		}
		return $config;
	}
	
	private function postgresFix($config, $schemaMap) {
		$config['schemaMap'] = $schemaMap;
		$config['on afterOpen'] = function ($event) use ($config) {
			$command = 'SET search_path TO ' . $config['schemaMap']['pgsql']['defaultSchema'];
			$event->sender->createCommand($command)->execute();
		};
		return $config;
	}
}
