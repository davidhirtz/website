<?php

declare(strict_types=1);

namespace App\Models;

use Hirtz\Cms\Models\Types\EntryType;
use Override;

/**
 * The project's entry. Its types, custom attributes and `i18nAttributes` are declared here or, where they need no
 * code of their own, on the container definition in `config/prod.php`.
 */
final class Entry extends \Hirtz\Cms\Models\Entry
{
    /**
     * A page rarely needs a publish date of its own, so the default type hides it. A type that schedules entries or
     * shows their date, such as news, declares its own fields in `getTypes()`.
     */
    #[Override]
    protected function getDefaultType(): EntryType
    {
        return parent::getDefaultType()->hiddenFields('publish_date');
    }
}
