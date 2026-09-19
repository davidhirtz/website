<?php

declare(strict_types=1);

// The rule set lives in `davidhirtz/yii2-dev`; the directories to rewrite are what differs between
// installations.

$config = require __DIR__ . '/vendor/davidhirtz/yii2-dev/config/rector.php';

return $config([
    __DIR__ . '/app',
    __DIR__ . '/config',
    __DIR__ . '/resources/views',
    __DIR__ . '/tests',
]);
