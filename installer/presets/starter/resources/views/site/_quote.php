<?php

declare(strict_types=1);

/**
 * The text as a quote, the heading as who said it.
 *
 * @see Section::TYPE_QUOTE
 *
 * @var View $this
 * @var Section[] $sections
 */

use App\Helpers\Html;
use App\Models\Section;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

foreach ($sections as $section) {
    $name = $section->getVisibleAttribute('name');
    ?>
    <section class="relative py-lg" id="<?= $section->getHtmlId(); ?>">
        <figure class="mx-auto grid max-w-3xl gap-sm text-center">
            <blockquote class="text-h3"><?= Html::prose($section); ?></blockquote>
            <?php if (is_string($name) && $name !== '') {
                ?>
                <figcaption class="text-sm">— <?= Html::encode($name); ?></figcaption>
                <?php
            } ?>
        </figure>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
