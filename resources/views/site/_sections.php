<?php

declare(strict_types=1);

/**
 * @see SectionStack
 *
 * @var View $this
 * @var Section[] $sections
 * @var SectionGroup $group
 */

use App\Helpers\Html;
use Hirtz\Cms\Models\Section;
use Hirtz\Cms\Widgets\Gallery;
use Hirtz\Cms\Widgets\SectionGroup;
use Hirtz\Cms\Widgets\SectionStack;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

// `relative` is the positioned ancestor the `AdminLink` overlay fills; the flex row around the sections comes
// from `SectionType::wrapper()`.
foreach ($sections as $section) {
    // A section carrying a block has no content of its own; the block is what it renders.
    $record = $section->getVisibleBlock() ?? $section;
    ?>
    <section class="relative <?= $section->getCssClass(); ?>" id="<?= $section->getHtmlId(); ?>">
        <?php if ($assets = $section->getVisibleAssets()) {
            echo Gallery::make()->assets($assets);
        } ?>
        <?= Html::prose($record); ?>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
