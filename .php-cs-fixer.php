<?php

declare(strict_types=1);

// The rules live in `davidhirtz/yii2-dev`; the directories to scan are what differs between installations.

$config = require __DIR__ . '/vendor/davidhirtz/yii2-dev/config/php-cs-fixer.php';

return $config([
    __DIR__ . '/app',
    __DIR__ . '/config',
    __DIR__ . '/resources/views',
    __DIR__ . '/tests',
]);
