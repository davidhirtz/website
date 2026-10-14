<?php

declare(strict_types=1);

/**
 * The section's images as a row that scrolls sideways and snaps to each image. Without JavaScript it is still
 * scrollable; `x-carousel` adds the previous and next buttons.
 *
 * @see Section::TYPE_CAROUSEL
 * @see resources/js/app/components/Carousel.ts
 *
 * @var View $this
 * @var Section[] $sections
 */

use App\Models\Section;
use Hirtz\Cms\Widgets\Artwork;
use Hirtz\Cms\Widgets\Gallery;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

foreach ($sections as $section) {
    ?>
    <section class="relative grid gap-sm py-lg" id="<?= $section->getHtmlId(); ?>">
        <?= $this->render('widgets/_text', ['section' => $section]); ?>
        <?php if ($assets = $section->getVisibleAssets()) {
            ?>
            <x-carousel class="grid gap-xs" aria-roledescription="carousel">
                <?= Gallery::make()
                    ->assets($assets)
                    ->attributes(['class' => 'flex snap-x snap-mandatory gap-sm overflow-x-auto [scrollbar-width:none]', 'data-track' => true])
                    ->artwork(static fn (Artwork $artwork): Artwork => $artwork->addClass('w-[85%] shrink-0 snap-center sm:w-[60%]')); ?>
                <div class="hidden justify-end gap-2" data-controls>
                    <button type="button" class="btn" data-prev aria-label="<?= Yii::t('app', 'CAROUSEL_PREVIOUS'); ?>">&larr;</button>
                    <button type="button" class="btn" data-next aria-label="<?= Yii::t('app', 'CAROUSEL_NEXT'); ?>">&rarr;</button>
                </div>
            </x-carousel>
            <?php
        } ?>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
