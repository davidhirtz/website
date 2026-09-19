<?php

declare(strict_types=1);

/**
 * @see Gallery
 *
 * @var View $this
 * @var Asset[] $assets
 */

use Hirtz\Cms\Widgets\Artwork;
use Hirtz\Cms\Widgets\Gallery;
use Hirtz\Media\Models\Asset;
use Hirtz\Skeleton\Web\View;

foreach ($assets as $asset) {
    echo Artwork::make()
        ->asset($asset);
}
