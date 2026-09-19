<?php

declare(strict_types=1);

use Hirtz\Skeleton\Web\Application;

$env = getenv('YII_ENV') ?: 'prod';

defined('YII_ENV') or define('YII_ENV', $env);
defined('YII_DEBUG') or define('YII_DEBUG', $env !== 'prod');

if (YII_DEBUG) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}

$maintenance = __DIR__ . '/../runtime/maintenance.php';

if (file_exists($maintenance)) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

(new Application(require __DIR__ . "/../config/$env.php"))->run();
