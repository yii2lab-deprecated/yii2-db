<?php

namespace yii2lab\db\domain\interfaces;

interface DriverInterface
{
	
	public function loadData($table);
	public function saveData($table, $data);
	public function getNameList();
	//public function beginTransaction();
	//public function commitTransaction();

}
