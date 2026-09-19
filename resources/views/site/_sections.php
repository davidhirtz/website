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

foreach ($sections as $section) {
    $record = $section->getVisibleBlock() ?? $section;
    ?>
    <section class="relative <?= $section->getCssClass(); ?>" id="<?= $section->getHtmlId(); ?>">
        <?php if ($assets = $record->getVisibleAssets()) {
            echo Gallery::make()->assets($assets);
        } ?>
        <?= Html::prose($record); ?>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
