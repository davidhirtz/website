<?php

declare(strict_types=1);

/**
 * The section's images in a grid of two columns, three from the `sm` breakpoint.
 *
 * @see Section::TYPE_GALLERY
 *
 * @var View $this
 * @var Section[] $sections
 */

use App\Models\Section;
use Hirtz\Cms\Widgets\Gallery;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

foreach ($sections as $section) {
    ?>
    <section class="relative grid gap-sm py-lg" id="<?= $section->getHtmlId(); ?>">
        <?= $this->render('widgets/_text', ['section' => $section]); ?>
        <?php if ($assets = $section->getVisibleAssets()) {
            echo Gallery::make()
                ->assets($assets)
                ->attributes(['class' => 'grid grid-cols-2 gap-sm sm:grid-cols-3']);
        } ?>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
