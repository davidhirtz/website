<?php

use app\controllers\SiteController;
use app\models\Asset;
use app\models\Entry;
use app\models\Section;
use app\modules\admin\widgets\forms\EntryActiveForm;

return [
    'name' => 'CMS Template',
    'components' => [
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'sitemap' => [
            'models' => [
                Entry::class,
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            \davidhirtz\yii2\cms\models\Asset::class => Asset::class,
            \davidhirtz\yii2\cms\models\Entry::class => Entry::class,
            \davidhirtz\yii2\cms\models\Section::class => Section::class,
            \davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm::class => EntryActiveForm::class,
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