<?php

declare(strict_types=1);

/**
 * A dark banner with the heading, a short text and the button, centered.
 *
 * @see Section::TYPE_CALL_TO_ACTION
 *
 * @var View $this
 * @var Section[] $sections
 */

use App\Models\Section;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

foreach ($sections as $section) {
    ?>
    <section class="relative my-lg grid justify-items-center gap-sm bg-neutral-900 p-lg text-center text-white" id="<?= $section->getHtmlId(); ?>">
        <?= $this->render('widgets/_text', ['section' => $section]); ?>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
