<?php

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(require(__DIR__ . '/prod.php'), [
    'components' => [
        'mailer' => [
            'useFileTransport' => true,
        ],
    ],
]);