<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Models\Entry;
use App\Models\Menus\SiteMenu;
use App\Models\Section;

trait EntryTrait
{
    /**
     * An entry with neither sections nor subentries has no route at all, so a linkable one gets a section.
     */
    protected function createEntry(
        string $name = 'Homepage',
        ?string $slug = null,
        ?SiteMenu $menu = null,
        ?Entry $parent = null,
        bool $withSection = true,
    ): Entry {
        $entry = Entry::create();
        $entry->name = $name;
        $entry->slug = $slug ?? (string)Entry::getModule()->entryIndexSlug;
        $entry->menu_ids = $menu ? [$menu->value] : null;
        $entry->parent_id = $parent?->id;

        self::assertTrue($entry->insert(), implode(' ', $entry->getFirstErrors()));

        if ($withSection) {
            $entry->populateSectionRelations([$this->createSection($entry, "$name section")]);
        }

        return $entry;
    }

    protected function createSection(Entry $entry, string $name = 'Intro', int $type = Section::TYPE_FULL_WIDTH): Section
    {
        $section = Section::instantiateByType($type);
        $section->name = $name;
        $section->populateEntryRelation($entry);

        self::assertTrue($section->insert(), implode(' ', $section->getFirstErrors()));

        return $section;
    }
}
