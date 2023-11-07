<?php

use app\controllers\SiteController;
use app\models\Asset;
use app\models\Entry;
use app\models\Section;
use app\modules\admin\widgets\forms\EntryActiveForm;

return [
    'name' => 'CMS Template',
    'components' => [
        'sitemap' => [
            'models' => [
                Entry::class,
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            'davidhirtz\yii2\cms\models\Asset' => Asset::class,
            'davidhirtz\yii2\cms\models\Entry' => Entry::class,
            'davidhirtz\yii2\cms\models\Section' => Section::class,
            'davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm' => EntryActiveForm::class,
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