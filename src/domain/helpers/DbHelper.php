<?php

namespace yii2lab\db\domain\helpers;

use yii2lab\app\domain\enums\AppEnum;
use yii2lab\app\domain\helpers\EnvService;
use yii2lab\db\domain\enums\DbDriverEnum;

class DbHelper {
	
	public static function getConfigFromEnv($name) {
		$connectionFromEnv = EnvService::getConnection($name);
		$connectionFromEnv = DbHelper::adapterConfig($connectionFromEnv);
		return $connectionFromEnv;
	}
	
	private static function adapterConfig($connection) {
		$connection = self::forgeMigrator($connection);
		if($connection['driver'] == DbDriverEnum::PGSQL) {
			$connection = PostgresHelper::postgresSchemaMap($connection);
		}
		
		if(empty($connection['dsn'])) {
			$connection['dsn'] = self::getDsn($connection);
		}

		$connection = self::clean($connection);
		return $connection;
	}
	
	private static function forgeMigrator($connection) {
		if(empty($connection['migrator'])) {
			return $connection;
		}
		$migrator = $connection['migrator'];
		unset($connection['migrator']);
		if(APP != AppEnum::CONSOLE) {
			return $connection;
		}
		foreach($migrator as $name => $value) {
			$connection[$name] = $value;
		}
		return $connection;
	}
	
	private static function getDsn($connection) {
		if($connection['driver'] == DbDriverEnum::SQLITE) {
			$connection['dsn'] = $connection['driver'] . ':' . $connection['dbname'];
		} else {
			$host = isset($connection['host']) ? $connection['host'] : 'localhost';
			$port = isset($connection['port']) ? "port={$connection['port']};"  : '';
			$connection['dsn'] = "{$connection['driver']}:host={$host};dbname={$connection['dbname']};{$port}";
		}

		return $connection['dsn'];
	}
	
	private static function clean($connection) {
		unset($connection['driver']);
		unset($connection['host']);
		unset($connection['dbname']);
		return $connection;
	}
	
}
