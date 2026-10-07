<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(require __DIR__ . '/prod.php', [
    'components' => [
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
