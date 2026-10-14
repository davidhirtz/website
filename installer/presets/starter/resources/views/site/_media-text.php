<?php

declare(strict_types=1);

/**
 * An image beside the text, the image first for "Image & text" and last for "Text & image". On a phone, the image
 * is always on top.
 *
 * @see Section::TYPE_IMAGE_TEXT
 * @see Section::TYPE_TEXT_IMAGE
 *
 * @var View $this
 * @var Section[] $sections
 */

use App\Models\Section;
use Hirtz\Cms\Widgets\Gallery;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

foreach ($sections as $section) {
    $assets = $section->getVisibleAssets();
    ?>
    <section class="relative grid items-center gap-md py-lg sm:grid-cols-2" id="<?= $section->getHtmlId(); ?>">
        <?php if ($assets) {
            ?>
            <div class="<?= $section->type === Section::TYPE_TEXT_IMAGE ? 'sm:order-last' : ''; ?>">
                <?= Gallery::make()->assets($assets)->limit(1); ?>
            </div>
            <?php
        } ?>
        <div class="grid gap-sm <?= $assets ? '' : 'sm:col-span-2'; ?>">
            <?= $this->render('widgets/_text', ['section' => $section]); ?>
        </div>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
