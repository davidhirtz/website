<?php

declare(strict_types=1);

/**
 * A run of consecutive accordion sections as one list: the heading opens and closes the text. Each section is a
 * `<details>`, so it works without JavaScript and the browser's find-in-page opens the one that matches.
 *
 * @see Section::TYPE_ACCORDION
 *
 * @var View $this
 * @var Section[] $sections
 */

use App\Helpers\Html;
use App\Models\Section;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

?>
<div class="mx-auto my-lg max-w-3xl divide-y border-y">
    <?php foreach ($sections as $section) {
        ?>
        <details class="group relative" id="<?= $section->getHtmlId(); ?>">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-sm py-sm font-bold [&::-webkit-details-marker]:hidden">
                <?= Html::encode($section->getVisibleAttribute('name')); ?>
                <span class="transition-transform group-open:rotate-45" aria-hidden="true">+</span>
            </summary>
            <?php // Not `Html::prose()`: its fade-in waits for the text to scroll into view, which a closed `<details>` never does?>
            <div class="prose pb-sm"><?= $section->getVisibleAttribute('content'); ?></div>
            <?= AdminLink::tag($section); ?>
        </details>
        <?php
    } ?>
</div>
