<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Models\Menus\SiteMenu;
use App\Models\Section;
use Hirtz\Cms\Modules\ModuleTrait;
use Override;
use Yii;

/**
 * Sets up the default entries through the admin, reachable at `/admin/cms/setup` once the module maps it.
 *
 * The home page shows one section of every type that needs no images, so a new installation has something to look
 * at and to edit; the imprint and the privacy policy are the pages the cookie banner links to. Their texts are
 * placeholders to be replaced before the site goes live.
 *
 * @noinspection PhpUnused
 */
final class SetupController extends \Hirtz\Cms\Modules\Admin\Controllers\SetupController
{
    use ModuleTrait;

    /**
     * @return list<array<string, mixed>>
     */
    #[Override]
    public function getCategoryAttributes(): array
    {
        return [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    #[Override]
    public function getEntryAttributes(): array
    {
        return [
            [
                'name' => 'Home',
                'slug' => self::getModule()->entryIndexSlug,
                'title' => Yii::$app->name,
                'sections' => [
                    [
                        'type' => Section::TYPE_HERO,
                        'name' => 'Welcome to ' . Yii::$app->name,
                        'content' => '<p>Add an image to this section in the admin to turn it into a full-width banner.</p>',
                        'link' => '#sections',
                        'link_text' => 'See the section types',
                    ],
                    [
                        'type' => Section::TYPE_FULL_WIDTH,
                        'name' => 'Section types',
                        'slug' => 'sections',
                        'content' => '<p>Every page is a stack of sections, and every section has a type that decides how it looks. Next to the ones on this page there are "Image & text", "Text & image", "Carousel", "Gallery" and "Teasers", which need images or linked pages to show anything.</p>',
                    ],
                    [
                        'type' => Section::TYPE_COLUMN,
                        'name' => 'Column',
                        'content' => '<p>Two column sections in a row share it, half the width each.</p>',
                    ],
                    [
                        'type' => Section::TYPE_COLUMN,
                        'name' => 'Another column',
                        'content' => '<p>On a phone, they are stacked.</p>',
                    ],
                    [
                        'type' => Section::TYPE_QUOTE,
                        'name' => 'Someone who said it',
                        'content' => '<p>A quote, with the heading as its source.</p>',
                    ],
                    [
                        'type' => Section::TYPE_ACCORDION,
                        'name' => 'What is an accordion?',
                        'content' => '<p>Consecutive accordion sections form one list, and each heading opens its text.</p>',
                    ],
                    [
                        'type' => Section::TYPE_ACCORDION,
                        'name' => 'Does it need JavaScript?',
                        'content' => '<p>No, each one is a details element the browser opens by itself.</p>',
                    ],
                    [
                        'type' => Section::TYPE_CALL_TO_ACTION,
                        'name' => 'Ready to start?',
                        'content' => '<p>Sign in to the admin to replace this content with your own.</p>',
                        'link' => '/admin',
                        'link_text' => 'Open the admin',
                    ],
                ],
            ],
            [
                'name' => 'Imprint',
                'slug' => 'imprint',
                'menu_ids' => [SiteMenu::Footer->value],
                'sections' => [
                    [
                        'type' => Section::TYPE_FULL_WIDTH,
                        'name' => 'Imprint',
                        'content' => '<p>Replace this text with the legal information about who runs this website.</p>',
                    ],
                ],
            ],
            [
                'name' => 'Privacy policy',
                'slug' => 'privacy',
                'menu_ids' => [SiteMenu::Footer->value],
                'sections' => [
                    [
                        'type' => Section::TYPE_FULL_WIDTH,
                        'name' => 'Privacy policy',
                        'content' => '<p>Replace this text with the privacy policy of this website.</p>',
                    ],
                ],
            ],
        ];
    }
}
