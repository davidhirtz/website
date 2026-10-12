<?php

declare(strict_types=1);

use Hirtz\Skeleton\Db\Dsn;
use yii\helpers\ArrayHelper;

$dsn = getenv('MYSQL_DSN') ?: 'mysql:host=127.0.0.1;dbname=cms_template_test';

// paratest numbers its workers, and each one runs against its own copy of the database
if ($token = getenv('TEST_TOKEN')) {
    $dsn = Dsn::fromString($dsn);
    $dsn = (string)new Dsn($dsn->driver, $dsn->host, "{$dsn->database}_$token", $dsn->port, $dsn->charset);
}

// The credentials of the gitignored development configuration, when there is one, so a local run needs no
// environment variables; CI has no `db.php` and sets them.
$db = is_file(__DIR__ . '/db.php') ? require __DIR__ . '/db.php' : [];

return ArrayHelper::merge(require __DIR__ . '/prod.php', [
    'components' => [
        'db' => [
            'dsn' => $dsn,
            'username' => getenv('MYSQL_USER') ?: ($db['username'] ?? 'root'),
            'password' => getenv('MYSQL_PASSWORD') ?: ($db['password'] ?? ''),
        ],
    ],
]);
