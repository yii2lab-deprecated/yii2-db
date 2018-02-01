<?php

namespace yii2lab\db\domain\filters\init;

use Yii;
use yii\base\BaseObject;
use yii2lab\designPattern\command\interfaces\CommandInterface;

class SetSequence extends BaseObject implements CommandInterface
{
	
	public $tableList;
	
	public function run() {
		$config = env('connection.main');
		$defaultSchema = $config['defaultSchema'];
		if($config['driver'] != 'pgsql') {
			return null;
		}
		foreach($this->tableList as $table => $key) {
			$sql = "SELECT pg_catalog.setval('$defaultSchema.$key', (SELECT max(id) from $defaultSchema.$table), true);";
			Yii::$app->db->createCommand($sql)->execute();
		}
	}
}
