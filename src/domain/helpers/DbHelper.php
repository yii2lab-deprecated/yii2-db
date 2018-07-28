<?php

namespace yii2lab\db\domain\helpers;

use yii2lab\misc\enums\DbDriverEnum;

class DbHelper
{
	
	public static function adapterConfig($connection)
	{
		if($connection['driver'] == DbDriverEnum::PGSQL) {
			$connection = self::postgresSchemaMap($connection);
		}
		if (empty($connection['dsn'])) {
			$connection['dsn'] = self::getDsn($connection);
		}
		$connection = self::clean($connection);
		return $connection;
	}
	
	private static function getDsn($connection) {
		if($connection['driver'] == DbDriverEnum::SQLITE) {
			$connection['dsn'] = $connection['driver'] . ':' . $connection['dbname'];
		} else {
			$connection['host'] = isset($connection['host']) ? $connection['host'] : 'localhost';
			$connection['dsn'] = $connection['driver'] . ':host=' . $connection['host'] . ';dbname=' . $connection['dbname'];
		}
		return $connection['dsn'];
	}
	
	private static function clean($connection)
	{
		unset($connection['driver']);
		unset($connection['host']);
		unset($connection['dbname']);
		unset($connection['defaultSchema']);
		return $connection;
	}
	
	private static function postgresSchemaMap($connection) {
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
		return $connection;
	}
	
	private static function postgresFix($connection, $schemaMap) {
		$connection['schemaMap'] = $schemaMap;
		$connection['on afterOpen'] = function ($event) use ($connection) {
			$command = 'SET search_path TO ' . $connection['schemaMap']['pgsql']['defaultSchema'];
			$event->sender->createCommand($command)->execute();
		};
		return $connection;
	}

}
