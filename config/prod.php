<?php

declare(strict_types=1);

use App\Controllers\SiteController;
use App\Models\Menus\SiteMenu;
use App\Modules\Admin\Controllers\SetupController;
use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Models\EntryAsset;
use Hirtz\Cms\Models\Menus\Menu;
use Hirtz\Cms\Models\Section;
use Hirtz\Cms\Models\SectionAsset;
use Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm;
use Hirtz\Cms\Modules\Admin\Widgets\Forms\SectionActiveForm;
use Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView;
use Hirtz\Cms\Sitemap\EntrySitemap;
use Hirtz\Skeleton\Consent\Categories\AnalyticsCategory;
use Hirtz\Skeleton\Consent\Categories\ExternalCategory;
use Hirtz\Skeleton\Consent\Categories\RequiredCategory;
use Hirtz\Skeleton\Consent\ConsentManager;
use Hirtz\Skeleton\Consent\Services\YouTube;
use Hirtz\Skeleton\Validators\HtmlValidator;

return [
    'name' => 'CMS Template',
    'components' => [
        // A closure, as the texts are translated
        'consent' => [
            'categories' => fn (ConsentManager $consent): array => [
                RequiredCategory::make(),
                AnalyticsCategory::make()->services(...$consent->getTagServices()),
                ExternalCategory::make()->services(YouTube::make()),
            ],
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'forceTranslation' => true,
                ],
            ],
        ],
        'sitemap' => [
            'sitemaps' => [
                EntrySitemap::class,
            ],
        ],
//        'urlManager' => [
//            'rules' => [],
//        ],
    ],
    'container' => [
        'definitions' => [
            Entry::class => App\Models\Entry::class,
            EntryActiveForm::class => App\Modules\Admin\Widgets\Forms\EntryActiveForm::class,
            EntryAsset::class => App\Models\EntryAsset::class,
            EntryGridView::class => App\Modules\Admin\Widgets\Grids\EntryGridView::class,
            HtmlValidator::class => [
                'allowedHtmlTags' => ['h1', 'h2', 'h3'],
                'allowedClasses' => ['a' => ['btn']],
                'removeUnnecessarySpanTags' => true,
            ],
            Section::class => App\Models\Section::class,
            SectionActiveForm::class => App\Modules\Admin\Widgets\Forms\SectionActiveForm::class,
            SectionAsset::class => App\Models\SectionAsset::class,
        ],
    ],
    'modules' => [
        'admin' => [
            'modules' => [
                'cms' => [
                    // `Cms\...\SetupController` is abstract, so the project's subclass is what the route resolves to.
                    'controllerMap' => [
                        'setup' => SetupController::class,
                    ],
                ],
            ],
        ],
        'cms' => [
            'controllerMap' => [
                'site' => [
                    'class' => SiteController::class,
                ],
            ],
            'enableNestedEntries' => true,
            'enableSectionEntries' => true,
            // A closure, because a menu's name is a `Yii::t()` result and this file is read before the application
            // has an `i18n` component. The values are what `Cms\Migrations\M260915200000MenuIds` writes when it
            // migrates a v2 `show_in_menu` / `show_in_footer` pair, so renaming them silently empties both menus.
            'menus' => fn (): array => [
                Menu::make(SiteMenu::Main)
                    ->name(Yii::t('app', 'MENU_MAIN')),
                Menu::make(SiteMenu::Footer)
                    ->name(Yii::t('app', 'MENU_FOOTER')),
            ],
            // The frontend views live in `resources/views`, not in the bundle, so `site/view`, `site/_sections`
            // and `layouts/main` are the project's own.
            'viewPath' => '@views',
        ],
        'media' => [
            // Pixel widths rather than media queries, so `Helpers\Size::breakpoint()` can build the `max-width`
            // condition a `sizes` attribute needs. They mirror the Tailwind breakpoints of `resources/css`.
            'breakpoints' => [
                'xs' => 352, // 22rem
                'sm' => 768, // 48rem
                'md' => 1024, // 64rem
                'lg' => 1280, // 80rem
                'xl' => 1600, // 100rem
            ],
            'transformations' => [
                // The scaled copies responsive images are served in, see docs/content-model.md#images.
            ],
        ],
    ],
];
