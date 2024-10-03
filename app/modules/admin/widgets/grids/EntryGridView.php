<?php

declare(strict_types=1);

namespace app\modules\admin\widgets\grids;

use davidhirtz\yii2\cms\modules\admin\widgets\grids\columns\MenuColumn;

class EntryGridView extends \davidhirtz\yii2\cms\modules\admin\widgets\grids\EntryGridView
{
    public function init(): void
    {
        $this->columns = [
            $this->statusColumn(),
            $this->showInMenuColumn(),
            $this->nameColumn(),
            $this->entryCountColumn(),
            $this->sectionCountColumn(),
            $this->assetCountColumn(),
            $this->dateColumn(),
            $this->buttonsColumn(),
        ];

        parent::init();
    }

    public function showInMenuColumn(): array
    {
        return [
            'class' => MenuColumn::class,
        ];
    }
}
