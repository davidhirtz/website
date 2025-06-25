<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/resources/views',
        __DIR__ . '/tests',
    ]);

    $ecsConfig->sets([
        SetList::COMMON,
        SetList::STRICT,
        SetList::PSR_12,
    ]);

    $ecsConfig->skip([
        NotOperatorWithSuccessorSpaceFixer::class
    ]);
};
