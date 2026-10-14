<?php

declare(strict_types=1);

namespace App\Models;

use Hirtz\Cms\Models\Types\SectionType;
use Hirtz\Media\Helpers\Size;
use Hirtz\Skeleton\Models\CustomAttributes\CustomAttribute;
use Hirtz\Skeleton\Models\CustomAttributes\TextCustomAttribute;
use Hirtz\Skeleton\Models\CustomAttributes\UrlCustomAttribute;
use Override;
use Yii;

/**
 * The section types of the starter preset. Each one beyond the two text types renders through a view of its own in
 * `resources/views/site`, named by `viewFile()`; consecutive sections of a type share one view call, which is what
 * lets `_accordion.php` put a run of questions into one list.
 *
 * A section has a heading (`name`), a text (`content`) and, for the types listed in `TYPES_WITH_BUTTON`, a button.
 * A type that has no use for images or linked entries says so with `allowAssets(false)` / `allowEntries(false)`, so
 * the admin does not offer them.
 *
 * @property string|null $link
 * @property string|null $link_text
 */
final class Section extends \Hirtz\Cms\Models\Section
{
    public const int TYPE_COLUMN = self::TYPE_DEFAULT;
    public const int TYPE_FULL_WIDTH = 2;
    public const int TYPE_HERO = 3;
    public const int TYPE_IMAGE_TEXT = 4;
    public const int TYPE_TEXT_IMAGE = 5;
    public const int TYPE_CAROUSEL = 6;
    public const int TYPE_GALLERY = 7;
    public const int TYPE_QUOTE = 8;
    public const int TYPE_CALL_TO_ACTION = 9;
    public const int TYPE_ACCORDION = 10;
    public const int TYPE_TEASERS = 11;

    public const array TYPES_WITH_BUTTON = [
        self::TYPE_HERO,
        self::TYPE_IMAGE_TEXT,
        self::TYPE_TEXT_IMAGE,
        self::TYPE_CALL_TO_ACTION,
    ];

    /**
     * @return list<SectionType>
     */
    #[Override]
    public function getTypes(): array
    {
        return [
            SectionType::make(self::TYPE_COLUMN)
                ->name(Yii::t('app', 'SECTION_TYPE_COLUMN'))
                ->cssClass('grid content-start gap-sm w-full py-md sm:w-1/2')
                ->wrapper('flex flex-wrap justify-center')
                ->sizes(Size::breakpoint('sm', '100vw'), '50vw')
                ->allowEntries(false),
            SectionType::make(self::TYPE_FULL_WIDTH)
                ->name(Yii::t('app', 'SECTION_TYPE_FULL_WIDTH'))
                ->cssClass('grid content-start gap-sm w-full py-md')
                ->wrapper('flex flex-wrap justify-center')
                ->sizes('100vw')
                ->allowEntries(false),
            SectionType::make(self::TYPE_HERO)
                ->name(Yii::t('app', 'SECTION_TYPE_HERO'))
                ->viewFile('_hero')
                ->sizes('100vw')
                ->allowEntries(false),
            SectionType::make(self::TYPE_IMAGE_TEXT)
                ->name(Yii::t('app', 'SECTION_TYPE_IMAGE_TEXT'))
                ->viewFile('_media-text')
                ->sizes(Size::breakpoint('sm', '100vw'), '50vw')
                ->allowEntries(false),
            SectionType::make(self::TYPE_TEXT_IMAGE)
                ->name(Yii::t('app', 'SECTION_TYPE_TEXT_IMAGE'))
                ->viewFile('_media-text')
                ->sizes(Size::breakpoint('sm', '100vw'), '50vw')
                ->allowEntries(false),
            SectionType::make(self::TYPE_CAROUSEL)
                ->name(Yii::t('app', 'SECTION_TYPE_CAROUSEL'))
                ->viewFile('_carousel')
                ->sizes(Size::breakpoint('sm', '85vw'), '60vw')
                ->allowEntries(false),
            SectionType::make(self::TYPE_GALLERY)
                ->name(Yii::t('app', 'SECTION_TYPE_GALLERY'))
                ->viewFile('_gallery')
                ->sizes(Size::breakpoint('sm', '50vw'), '33vw')
                ->allowEntries(false),
            SectionType::make(self::TYPE_QUOTE)
                ->name(Yii::t('app', 'SECTION_TYPE_QUOTE'))
                ->viewFile('_quote')
                ->allowAssets(false)
                ->allowEntries(false),
            SectionType::make(self::TYPE_CALL_TO_ACTION)
                ->name(Yii::t('app', 'SECTION_TYPE_CALL_TO_ACTION'))
                ->viewFile('_call-to-action')
                ->allowAssets(false)
                ->allowEntries(false),
            SectionType::make(self::TYPE_ACCORDION)
                ->name(Yii::t('app', 'SECTION_TYPE_ACCORDION'))
                ->viewFile('_accordion')
                ->allowAssets(false)
                ->allowEntries(false),
            SectionType::make(self::TYPE_TEASERS)
                ->name(Yii::t('app', 'SECTION_TYPE_TEASERS'))
                ->viewFile('_teasers')
                ->sizes(Size::breakpoint('sm', '100vw'), '33vw')
                ->allowAssets(false),
        ];
    }

    public function hasButton(): bool
    {
        return in_array($this->type, self::TYPES_WITH_BUTTON, true);
    }

    /**
     * @return list<CustomAttribute>
     */
    #[Override]
    protected function getDefaultCustomAttributes(): array
    {
        return [
            ...parent::getDefaultCustomAttributes(),
            UrlCustomAttribute::make('link')
                ->label(Yii::t('app', 'SECTION_LINK_LABEL'))
                ->relative()
                ->translatable($this->isTranslatableAttribute('link'))
                ->visible(static fn (self $section): bool => $section->hasButton()),
            TextCustomAttribute::make('link_text')
                ->label(Yii::t('app', 'SECTION_LINK_TEXT_LABEL'))
                ->max(50)
                ->translatable($this->isTranslatableAttribute('link_text'))
                ->visible(static fn (self $section): bool => $section->hasButton()),
        ];
    }
}
