<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(require __DIR__ . '/prod.php', [
    'components' => [
        // The database of `compose.yaml`, ahead of a `config/db.php` that names this machine's own server
        'db' => array_filter([
            'dsn' => getenv('DB_DSN'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
        ]),
        'mailer' => [
            'useFileTransport' => true,
        ],
        'session' => [
            'cookieSecure' => false,
        ],
        'user' => [
            'cookieSecure' => false,
        ],
    ],
]);
