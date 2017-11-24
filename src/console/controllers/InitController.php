<?php

namespace yii2lab\db\console\controllers;

use yii2lab\console\yii\console\Controller;
use Yii;
use yii2lab\console\helpers\input\Select;
use yii2lab\console\helpers\Output;
use yii2lab\db\domain\helpers\CopyHelper;
use yii2lab\db\domain\helpers\GrantHelper;
use yii2lab\domain\helpers\ReflectionHelper;
use yii2module\fixture\helpers\Fixtures;

class InitController extends Controller
{
	
	const ACTION_SET_GRANT = 'Set Grant';
	const ACTION_IMPORT_FIXTURE = 'Import Fixture';
	const ACTION_MIGRATE_DATA = 'Migrate data';
	
	public function actionIndex($selectedActions = null)
	{
		if(empty($selectedActions)) {
			$allActions = $this->allActions();
			$selectedActions = Select::display('Select actions', $allActions, true);
		}
		if(in_array(self::ACTION_SET_GRANT, $selectedActions)) {
			$this->actionSetGrant();
		}
		if(in_array(self::ACTION_IMPORT_FIXTURE, $selectedActions)) {
			$this->actionImportFixture();
		}
		if(in_array(self::ACTION_MIGRATE_DATA, $selectedActions)) {
			$this->actionMigrateData();
		}
	}
	
	public function actionAll()
	{
		$allActions = $this->allActions();
		$this->actionIndex($allActions);
	}
	
	public function actionSetGrant() {
		GrantHelper::run($this->module->grantUser);
		Output::block("DB granted!");
	}
	
	public function actionImportFixture() {
		$fixtures = Yii::createObject(Fixtures::className());
		$tables = $fixtures->import($this->module->importFixture);
		Output::items($tables, 'Imported tables');
	}
	
	public function actionMigrateData() {
		CopyHelper::run($this->module->migrateData);
		Output::block("Data migrated!");
	}
	
	private function allActions() {
		return ReflectionHelper::getConstantsValuesByPrefix($this,'action');
	}
	
}
