<?php

declare(strict_types=1);

use Hirtz\Skeleton\Web\Application;

$env = getenv('YII_ENV') ?: 'prod';

defined('YII_ENV') or define('YII_ENV', $env);
defined('YII_DEBUG') or define('YII_DEBUG', $env !== 'prod');
defined('YII_ENV_DEV') or define('YII_ENV_DEV', in_array($env, ['dev', 'local'], true));

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

// named here, where it is known: the application would otherwise derive it from the request, and PHP's built-in
// server names the requested file; a config's own `basePath` wins
(new Application(['basePath' => dirname(__DIR__), ...require __DIR__ . "/../config/$env.php"]))->run();
