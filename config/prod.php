<?php

use app\controllers\SiteController;
use davidhirtz\yii2\cms\models\Asset;
use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\models\Section;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm;
use davidhirtz\yii2\cms\widgets\MetaTags;
use davidhirtz\yii2\skeleton\validators\HtmlValidator;

return [
    'name' => 'CMS Template',
    'components' => [
        'sitemap' => [
            'models' => [
                \app\models\Entry::class,
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            Asset::class => \app\models\Asset::class,
            Entry::class => \app\models\Entry::class,
            Section::class => \app\models\Section::class,
            EntryActiveForm::class => \app\modules\admin\widgets\forms\EntryActiveForm::class,

            MetaTags::class => [
                'transformationName' => 'md',
            ],

            HtmlValidator::class => [
                'allowedHtmlTags' => ['h1', 'h2', 'h3'],
                'allowedClasses' => ['btn'],
            ],
        ],
    ],
    'modules' => [
        'cms' => [
            'enableNestedEntries' => true,
            'enableSectionEntries' => true,
            'controllerMap' => [
                'site' => [
                    'class' => SiteController::class,
                ],
            ],
        ],
        'media' => [
            'transformations' => [
                // TODO: Add your transformations here.
            ],
        ],
    ],
];