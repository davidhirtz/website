<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(require('./config/prod.php'), [
    'components' => [
        'db' => [
            'dsn' => getenv('MYSQL_DSN') ?: 'mysql:host=127.0.0.1;dbname=' . getenv('MYSQL_DATABASE'),
            'username' => getenv('MYSQL_USER') ?: 'root',
            'password' => getenv('MYSQL_PASSWORD') ?: '',
            'charset' => 'utf8',
        ],
        'request' => [
            'scriptFile' => './web/index.php',
        ],
    ],
    'params' => [
        'cookieValidationKey' => 'test',
    ],
]);
