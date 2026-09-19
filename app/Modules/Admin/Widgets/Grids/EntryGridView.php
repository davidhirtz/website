<?php

declare(strict_types=1);

namespace App\Modules\Admin\Widgets\Grids;

use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Modules\Admin\Widgets\Grids\Columns\MenuColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\Column;
use Override;

/**
 * @template T of Entry
 * @extends \Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView<T>
 */
final class EntryGridView extends \Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView
{
    #[Override]
    protected function configure(): void
    {
        $this->columns ??= [
            $this->getStatusColumn(),
            $this->getMenuColumn(),
            $this->getTypeColumn(),
            $this->getNameColumn(),
            $this->getEntryCountColumn(),
            $this->getSectionCountColumn(),
            $this->getAssetCountColumn(),
            $this->getDateColumn(),
            $this->getButtonColumn(),
        ];

        parent::configure();
    }

    /**
     * Renders nothing while no entry on the page is in a menu, so a project with none pays no column for it.
     */
    protected function getMenuColumn(): Column
    {
        return MenuColumn::make();
    }
}
