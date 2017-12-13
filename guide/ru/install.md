Установка
===

Устанавливаем зависимость:

```
composer require yii2lab/yii2-db
```

Объявляем модуль:

```php
return [
	'modules' => [
		// ...
		'db' => [
			'class' => 'yii2lab\db\console\Module',
			'actions' => [
				'ImportFixture' => [
					'tableList' => [
						'user',
						'user_assignment',
						'rest',
					],
				],
				'common\init\db\Subject2user', // можно назначать кастомный скрипт
				'SetGrant' => [
					'grantUser' => 'logging',
				],
				'SetSequence' => [
					'tableList' => [
						'user' => 'user_id_seq',
					],
				],
			],
		],
		// ...
	],
];
```
