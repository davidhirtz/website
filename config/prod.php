<?php

declare(strict_types=1);

use app\controllers\SiteController;
use davidhirtz\yii2\cms\models\Asset;
use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\models\Section;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\AssetActiveForm;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\SectionActiveForm;
use davidhirtz\yii2\cms\modules\admin\widgets\grids\EntryGridView;
use davidhirtz\yii2\cms\widgets\Canvas;
use davidhirtz\yii2\cms\widgets\MetaTags;
use davidhirtz\yii2\skeleton\assets\AdminAsset;
use davidhirtz\yii2\skeleton\validators\HtmlValidator;
use davidhirtz\yii2\vite\components\Vite;

return [
    'name' => 'CMS Template',
    'components' => [
        'assetManager' => [
            'bundles' => [
                AdminAsset::class => [
                    'faviconOptions' => [
                        'href' => '/images/favicons/favicon.svg',
                        'sizes' => 'any',
                        'type' => 'image/svg+xml',
                        'rel' => 'icon',
                    ],
                ],
            ],
        ],
        'sitemap' => [
            'models' => [
                \app\models\Entry::class,
            ],
        ],
//        'urlManager' => [
//            'rules' => [],
//        ],
        'vite' => [
            'class' => Vite::class,
        ],
    ],
    'container' => [
        'definitions' => [
            Asset::class => \app\models\Asset::class,
            AssetActiveForm::class => \app\modules\admin\widgets\forms\AssetActiveForm::class,
            Canvas::class => \app\widgets\Canvas::class,
            Entry::class => \app\models\Entry::class,
            EntryActiveForm::class => \app\modules\admin\widgets\forms\EntryActiveForm::class,
            EntryGridView::class => \app\modules\admin\widgets\grids\EntryGridView::class,
            HtmlValidator::class => [
                'allowedHtmlTags' => ['h1', 'h2', 'h3'],
                'allowedClasses' => ['btn'],
                'removeUnnecessarySpanTags' => true,
            ],
            MetaTags::class => [
                'transformationName' => 'md',
            ],
            Section::class => \app\models\Section::class,
            SectionActiveForm::class => \app\modules\admin\widgets\forms\SectionActiveForm::class,
        ],
    ],
    'modules' => [
        'cms' => [
            'controllerMap' => [
                'site' => [
                    'class' => SiteController::class,
                ],
            ],
            'enableNestedEntries' => true,
            'enableSectionEntries' => true,
        ],
        'media' => [
            'breakpoints' => [
                'xs' => '(min-width:22rem)', // 352px
                'sm' => '(min-width:48rem)', // 768px
                'md' => '(min-width:64rem)', // 1024px
                'lg' => '(min-width:80rem)', // 1280px
                'xl' => '(min-width:100rem)', // 1600px
            ],
            'transformations' => [
                // TODO: Add your transformations here.
            ],
        ],
    ],
];