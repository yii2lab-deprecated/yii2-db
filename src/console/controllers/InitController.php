<?php

namespace yii2lab\db\console\controllers;

use yii2lab\console\yii\console\Controller;
use yii2lab\db\domain\helpers\CallbackHelper;

class InitController extends Controller
{
	
	public function actionIndex()
	{
		$this->actionAll();
	}
	
	public function actionAll()
	{
		CallbackHelper::run($this->module->actions);
	}
	
}
