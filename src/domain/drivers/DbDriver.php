<?php

namespace yii2lab\db\domain\drivers;

use Yii;
use yii2lab\helpers\Helper;
use yii2lab\db\domain\interfaces\DriverInterface;

class DbDriver implements DriverInterface
{
	const DRIVER_NAMESPACE = 'yii2lab\db\domain\drivers\db';
	private $driver;
	
	public function __construct()
	{
		$db = Helper::getCurrentDbDriver();
		$this->driver = Yii::createObject(self::DRIVER_NAMESPACE . '\\' . ucfirst($db) . 'Driver');
	}
	
	public function loadData($table)
	{
		return $this->driver->loadData($table);
	}

	public function saveData($table, $data)
	{
		return $this->driver->saveData($table, $data);
	}
	
	public function getNameList()
	{
		return $this->driver->getNameList();
	}

}
