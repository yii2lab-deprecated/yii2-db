<?php

namespace yii2lab\db\console\controllers;

use yii2lab\console\base\Controller;
use yii2lab\designPattern\scenario\helpers\ScenarioHelper;

class InitController extends Controller
{
	
	/**
	 * Use custom scripts when the project is initialized
	 *
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\web\ServerErrorHttpException
	 */
	public function actionIndex()
	{
		ScenarioHelper::runAll($this->module->actions);
	}
	
}
