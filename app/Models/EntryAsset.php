<?php

declare(strict_types=1);

namespace App\Models;

use Hirtz\Skeleton\Models\CustomAttributes\CustomAttribute;
use Override;

/**
 * An entry asset is the page's own imagery: it carries an alt text and an optional link, and nothing else. A
 * definition left out here is neither rendered nor saved, so a value stored under it survives untouched.
 */
final class EntryAsset extends \Hirtz\Cms\Models\EntryAsset
{
    /**
     * @return list<CustomAttribute>
     */
    #[Override]
    protected function getDefaultCustomAttributes(): array
    {
        $names = ['alt_text', 'link'];

        return array_values(array_filter(
            parent::getDefaultCustomAttributes(),
            static fn (CustomAttribute $definition): bool => in_array($definition->name, $names, true),
        ));
    }
}
