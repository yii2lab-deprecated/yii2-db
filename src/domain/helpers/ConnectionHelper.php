<?php

namespace yii2lab\db\domain\helpers;

use yii2lab\helpers\Helper;
use yii2lab\helpers\UrlHelper;
use yii2lab\db\domain\enums\DbDriverEnum;
use Yii;
use yii\db\Exception;
use yii\db\Connection;
use yii2lab\db\domain\helpers\DbHelper;
use yii2lab\domain\exceptions\UnprocessableEntityHttpException;
use yii2lab\domain\helpers\ErrorCollection;

class ConnectionHelper
{
	
	public static function test(array $config) {
		//$config = DbHelper::normalizeConfig($config);
		//$config = DbHelper::schemaMap($config);
		$connection = Yii::createObject(Connection::class);
		$connection->dsn = $config['dsn'];
		$connection->username = $config['username'];
		$connection->password = $config['password'];
		try {
			$connection->open();
		} catch(Exception $e) {
			$message = $e->getMessage();
			$message = trim($message);
			$error = new ErrorCollection();
			$previous2 = $e->getPrevious()->getPrevious();
			if($previous2 != null && preg_match('~getaddrinfo failed~', $previous2->getMessage())) {
				$error->add('host', Yii::t('app/connection', 'bad_host'));
			}
			if(preg_match('~Unknown database~', $message)) {
				$error->add('dbname', Yii::t('app/connection', 'bad_dbname'));
			}
			if(preg_match('~Access denied for user~', $message)) {
				$error->add('username', Yii::t('app/connection', 'bad_username'));
				$error->add('password', Yii::t('app/connection', 'bad_password'));
			}
			throw new UnprocessableEntityHttpException($error);
		}
	}
	
	public static function getDriverFromDb(Connection $db)
	{
		$parsed = self::parseDsn($db->dsn);
		return $parsed['driver'];
	}
	
	public static function parseDsn($dsn)
	{
		preg_match('#([a-z]+):(.+)#', $dsn, $matches);
		$result['driver'] = $matches[1];
		$result['path'] = $matches[2];
		$result['params'] = Helper::parseParams($result['path'], ';', '=');
		return $result;
	}
	
}
