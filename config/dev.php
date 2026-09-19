<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(require __DIR__ . '/prod.php', [
    'components' => [
        'mailer' => [
            'useFileTransport' => true,
        ],
        // The dev server is plain http, and a `Secure` cookie planted by an https visit to the same host name
        // would otherwise block every later `Set-Cookie` under that name.
        'session' => [
            'cookieSecure' => false,
        ],
        'user' => [
            'cookieSecure' => false,
        ],
    ],
]);
