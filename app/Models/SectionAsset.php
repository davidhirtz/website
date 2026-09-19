<?php

declare(strict_types=1);

namespace App\Models;

use Hirtz\Skeleton\Models\CustomAttributes\CustomAttribute;
use Override;

/**
 * A section asset may carry a caption, which `Widgets\Artwork` renders as the figure's `<figcaption>`, and an
 * embed url, for which `Artwork` renders `site/widgets/_embed`.
 */
final class SectionAsset extends \Hirtz\Cms\Models\SectionAsset
{
    /**
     * @return list<CustomAttribute>
     */
    #[Override]
    protected function getDefaultCustomAttributes(): array
    {
        $names = ['content', 'alt_text', 'link', 'embed_url'];

        return array_values(array_filter(
            parent::getDefaultCustomAttributes(),
            static fn (CustomAttribute $definition): bool => in_array($definition->name, $names, true),
        ));
    }
}
