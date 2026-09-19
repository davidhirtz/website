<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PHP8x3Migration:risky' => true,
    ])
    ->setCacheFile(__DIR__ . '/runtime/.php-cs-fixer.cache')
    ->setFinder(
        (new Finder())
            ->in([
                __DIR__ . '/app',
                __DIR__ . '/config',
                __DIR__ . '/resources/views',
                __DIR__ . '/tests',
            ])
            // written by `./yii params` and never committed
            ->notName(['db.php', 'local.php', 'params.php'])
    );
