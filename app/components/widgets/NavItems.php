<?php

namespace app\components\widgets;

use app\models\Entry;
use Yii;

class NavItems
{
    /**
     * @var Entry[]
     */
    private static ?array $_entries = null;

    /**
     * @return Entry[]
     */
    public static function getMenuItems(): array
    {
        return array_filter(static::getEntries(), fn($entry) => $entry->show_in_menu);
    }

    /**
     * @param Entry $parent
     * @return Entry[]
     */
    public static function getSubmenuItems(Entry $parent): array
    {
        return !$parent->entry_count ? [] : array_filter(static::getMenuItems(), function (Entry $entry) use ($parent) {
            return $entry->parent_id == $parent->id;
        });
    }

    /**
     * @return Entry[]
     */
    public static function getFooterItems(): array
    {
        return array_filter(static::getEntries(), fn($entry) => $entry->show_in_footer);
    }

    /**
     * @return Entry[]
     */
    protected static function getEntries(): array
    {
        if (static::$_entries === null) {
            Yii::debug('Loading menu items ...');

            static::$_entries = Entry::find()
                ->select(['id', 'parent_id', 'position', 'name', 'slug', 'parent_slug', 'section_count', 'show_in_menu', 'show_in_footer', 'entry_count'])
                ->replaceI18nAttributes()
                ->addSelect(Entry::instance()->getI18nAttributesNames(['slug', 'parent_slug']))
                ->where(['or', ['show_in_menu' => 1], ['show_in_footer' => 1]])
                ->whereStatus()
                ->orderBy(['position' => SORT_ASC])
                ->indexBy('id')
                ->all();
        }

        return static::$_entries;
    }
}