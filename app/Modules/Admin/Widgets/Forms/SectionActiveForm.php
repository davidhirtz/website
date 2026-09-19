<?php

declare(strict_types=1);

namespace App\Modules\Admin\Widgets\Forms;

use App\Models\Section;
use Hirtz\Skeleton\Widgets\Forms\Fieldset;
use Override;

/**
 * @property Section $model
 */
final class SectionActiveForm extends \Hirtz\Cms\Modules\Admin\Widgets\Forms\SectionActiveForm
{
    /**
     * @return list<Fieldset>|list<list<mixed>>
     */
    #[Override]
    protected function getDefaultRows(): array
    {
        return [
            [
                $this->getStatusField(),
                $this->getTypeField(),
                $this->getBlockIdField(),
            ],
            [
                ...$this->getCustomAttributeFields(except: ['slug']),
            ],
            [
                $this->getSlugField(),
            ],
        ];
    }
}
