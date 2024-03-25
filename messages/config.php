<?php

return [
    'except' => [
        '/config',
        '/messages',
        '/node_modules',
        '/runtime',
        '/tests',
        '/vendor',
        '/web',
    ],
    'format' => 'php',
    'languages' => ['de', 'en-US'],
    'messagePath' => __DIR__,
    'ignoreCategories' => [
        'cms',
        'hotspot',
        'media',
        'skeleton',
        'yii',
    ],
    'overwrite' => true,
    'only' => ['*.php'],
    'removeUnused' => true,
    'sort' => true,
    'sourcePath' => dirname(__DIR__),
];
