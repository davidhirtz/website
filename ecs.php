<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/resources/views',
    ]);

    $ecsConfig->sets([
        SetList::CLEAN_CODE,
        SetList::COMMENTS,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::PSR_12,
        SetList::STRICT,
    ]);

    $ecsConfig->skip([
        NotOperatorWithSuccessorSpaceFixer::class,
        ExplicitStringVariableFixer::class,
        OrderedClassElementsFixer::class,
    ]);

    $ecsConfig->ruleWithConfiguration(ClassAttributesSeparationFixer::class, [
        'elements' => ['method' => 'one'],
    ]);
};
