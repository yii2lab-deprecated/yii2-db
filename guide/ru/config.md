Конфигурация
===

Объявить конфиг в `main.php`:

```php
return [
	...
	'controllerMap' => [
		'migrate' => [
			'class' => 'dee\console\MigrateController',
			'migrationPath' => '@console/migrations',
			'generatorTemplateFiles' => [
				'create_table' => '@yii2lab/db/domain/yii/views/createTableMigration.php',
			],
		],
	],
	...
];
```
