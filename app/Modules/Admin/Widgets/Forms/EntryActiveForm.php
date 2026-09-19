<?php

declare(strict_types=1);

namespace App\Modules\Admin\Widgets\Forms;

use App\Models\Entry;
use Hirtz\Skeleton\Widgets\Forms\Fieldset;
use Hirtz\Tenant\Models\Collections\TenantCollection;
use Override;

/**
 * @property Entry $model
 */
final class EntryActiveForm extends \Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm
{
    /**
     * Replacing the rows wholesale means owning the tenant field the defaults contributed: with several tenants it
     * belongs first, because it decides which parents and which URL the rest of the form shows.
     *
     * @return list<Fieldset>|list<list<mixed>>
     */
    #[Override]
    protected function getDefaultRows(): array
    {
        $tenantIdRow = [$this->getTenantIdField()];

        $rows = [
            [
                $this->getStatusField(),
                $this->getTypeField(),
                $this->getParentIdField(),
                $this->getNameField(),
                ...$this->getCustomAttributeFields(),
                $this->getPublishDateField(),
            ],
            [
                $this->getTitleField(),
                $this->getDescriptionField(),
                $this->getSlugField(),
                $this->getMenuIdsField(),
            ],
        ];

        return count(TenantCollection::getAll()) > 1
            ? [$tenantIdRow, ...$rows]
            : [...$rows, $tenantIdRow];
    }
}
