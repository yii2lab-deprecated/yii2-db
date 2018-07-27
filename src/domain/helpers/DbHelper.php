<?php

namespace yii2lab\db\domain\helpers;

use yii2lab\misc\enums\DbDriverEnum;

class DbHelper
{
	
	public static function schemaMap($config) {
		if($config['driver'] != DbDriverEnum::PGSQL) {
			unset($config['schemaMap']);
			return $config;
		}
		if(!empty($config['schemaMap'])) {
			$config = self::postgresFix($config, $config['schemaMap']);
		}
		if($config['driver'] == 'pgsql') {
			$schemaMap =  [
				'pgsql' => [
					'class' => 'yii\db\pgsql\Schema',
					'defaultSchema' => $config['defaultSchema'],
				]
			];
			$config = self::postgresFix($config, $schemaMap);
		}
		return $config;
	}
	
	public static function normalizeConfig($db)
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
	
	private static function postgresFix($config, $schemaMap) {
		$config['schemaMap'] = $schemaMap;
		$config['on afterOpen'] = function ($event) use ($config) {
			$command = 'SET search_path TO ' . $config['schemaMap']['pgsql']['defaultSchema'];
			$event->sender->createCommand($command)->execute();
		};
		return $config;
	}
	
}
