<?php

namespace yii2lab\db\console\controllers;

use yii2lab\console\yii\console\Controller;
use yii2lab\db\domain\helpers\CallbackHelper;

class InitController extends Controller
{
	
	/**
	 * Use custom scripts when the project is initialized
	 */
	public function actionIndex()
	{
		CallbackHelper::run($this->module->actions);
	}
	
}
