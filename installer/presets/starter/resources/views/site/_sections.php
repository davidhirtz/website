<?php

declare(strict_types=1);

/**
 * The text sections ("Column" and "Full width"), and every type without a view of its own.
 *
 * @see SectionStack
 *
 * @var View $this
 * @var Section[] $sections
 * @var SectionGroup $group
 */

use App\Models\Section;
use Hirtz\Cms\Widgets\Gallery;
use Hirtz\Cms\Widgets\SectionGroup;
use Hirtz\Cms\Widgets\SectionStack;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

foreach ($sections as $section) {
    ?>
    <section class="relative <?= $section->getCssClass(); ?>" id="<?= $section->getHtmlId(); ?>">
        <?php if ($assets = $section->getVisibleAssets()) {
            echo Gallery::make()->assets($assets);
        } ?>
        <?= $this->render('widgets/_text', ['section' => $section]); ?>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
