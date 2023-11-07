<?php

return [
    'sourcePath' => dirname(__DIR__),
    'messagePath' => __DIR__,
    'languages' => ['de', 'en-US'],
    'ignoreCategories' => [
        'cms',
        'hotspot',
        'media',
        'skeleton',
        'yii',
    ],
    'overwrite' => true,
    'only' => ['*.php'],
    'format' => 'php',
    'sort' => true,
    'except' => [
        '/config',
        '/messages',
        '/node_modules',
        '/runtime',
        '/tests',
        '/vendor',
        '/web',
    ],
];
