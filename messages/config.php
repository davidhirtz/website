<?php

declare(strict_types=1);

/**
 * Configuration for `./yii message`, which reads it by default. It spreads the skeleton's for the translator list,
 * the format and the file header, and narrows the source to this project's own files: the `app` category is the
 * project's alone.
 */
return [
    ...require Yii::getAlias('@skeleton/../messages/config.php'),
    'sourcePath' => dirname(__DIR__),
    'messagePath' => __DIR__,
    'categories' => ['app'],
    'languages' => ['de', 'en-US'],
    'only' => ['*.php'],
    // `config` is *not* excluded: `modules.cms.menus` declares its names through `Yii::t()`, and `removeUnused`
    // would drop every key the scan cannot see.
    'except' => [
        // A preset brings its own language files, which it copies over these.
        'installer',
        'messages',
        'node_modules',
        'runtime',
        'tests',
        'vendor',
        'web',
    ],
];
