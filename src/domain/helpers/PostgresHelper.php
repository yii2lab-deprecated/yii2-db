<?php

namespace yii2lab\db\domain\helpers;

use yii\base\Event;
use yii2lab\app\domain\helpers\EnvService;
use yii2lab\db\domain\enums\DbDriverEnum;

class PostgresHelper {
	
	public static function getDefaultSchema($connectionName = 'main') {
		$config = EnvService::getConnection($connectionName);
		if($config['driver'] != DbDriverEnum::PGSQL) {
			return null;
		}
		if(empty($config['defaultSchema'])) {
			return null;
		}
		return $config['defaultSchema'];
	}
	
	public static function postgresSchemaMap($connection) {
		if(!empty($connection['schemaMap'])) {
			$schemaMap = $connection['schemaMap'];
		} else {
			$schemaMap = [
				'pgsql' => [
					'class' => 'yii\db\pgsql\Schema',
					'defaultSchema' => $connection['defaultSchema'],
				],
			];
		}
		$connection = self::postgresFix($connection, $schemaMap);
		$connection = self::clean($connection);
		return $connection;
	}
	
	private static function postgresFix($connection, $schemaMap) {
		$connection['schemaMap'] = $schemaMap;
		$connection['on afterOpen'] = function (Event $event) use ($connection) {
			$command = 'SET search_path TO ' . $connection['schemaMap']['pgsql']['defaultSchema'];
			$event->sender->createCommand($command)->execute();
		};
		return $connection;
	}
	
	private static function clean($connection) {
		unset($connection['defaultSchema']);
		return $connection;
	}
}
