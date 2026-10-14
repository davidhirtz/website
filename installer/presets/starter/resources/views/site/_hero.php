<?php

declare(strict_types=1);

/**
 * A full-bleed banner: the section's first image as the background, with the heading as the page's `<h1>`.
 *
 * @see Section::TYPE_HERO
 *
 * @var View $this
 * @var Section[] $sections
 */

use App\Models\Section;
use Hirtz\Cms\Widgets\Artwork;
use Hirtz\Media\Widgets\Media;
use Hirtz\Skeleton\Html\Img;
use Hirtz\Skeleton\Html\Picture;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

foreach ($sections as $section) {
    $asset = $section->getVisibleAssets()[0] ?? null;
    ?>
    <section class="relative -mx-sm grid min-h-[70svh] items-end overflow-hidden bg-neutral-900 text-white" id="<?= $section->getHtmlId(); ?>">
        <?php if ($asset) {
            echo Artwork::make()
                ->asset($asset)
                ->aspectRatio(false)
                ->class('absolute inset-0')
                ->media(static fn (Media $media): Media => $media
                    ->picture(static fn (Picture $picture): Picture => $picture->addClass('contents'))
                    ->image(static fn (Img $image): Img => $image->addClass('size-full object-cover')));
            ?>
            <div class="absolute inset-0 bg-linear-to-t from-black/70 to-transparent"></div>
            <?php
        } ?>
        <div class="relative grid max-w-3xl gap-sm p-lg">
            <?= $this->render('widgets/_text', ['section' => $section, 'heading' => 'h1']); ?>
        </div>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
