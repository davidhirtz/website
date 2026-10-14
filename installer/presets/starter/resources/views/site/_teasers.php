<?php

declare(strict_types=1);

/**
 * The entries linked to the section ("Entries" tab in the admin) as cards: the entry's first image, its name and
 * its meta description.
 *
 * @see Section::TYPE_TEASERS
 *
 * @var View $this
 * @var Section[] $sections
 */

use App\Helpers\Html;
use App\Models\Section;
use Hirtz\Cms\Widgets\Artwork;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\AdminLink;

foreach ($sections as $section) {
    ?>
    <section class="relative grid gap-sm py-lg" id="<?= $section->getHtmlId(); ?>">
        <?= $this->render('widgets/_text', ['section' => $section]); ?>
        <?php if ($entries = $section->getVisibleEntries()) {
            ?>
            <ul class="grid gap-md sm:grid-cols-3">
                <?php foreach ($entries as $entry) {
                    $asset = $entry->getVisibleAssets()[0] ?? null;
                    $description = $entry->getI18nAttribute('description');
                    ?>
                    <li class="relative grid content-start gap-xs">
                        <?php if ($asset) {
                            echo Artwork::make()->asset($asset);
                        } ?>
                        <h3 class="text-h4 font-bold">
                            <?= Html::link($entry, ['class' => 'after:absolute after:inset-0']); ?>
                        </h3>
                        <?php if ($description) {
                            ?>
                            <p class="text-sm"><?= Html::encode($description); ?></p>
                            <?php
                        } ?>
                    </li>
                    <?php
                } ?>
            </ul>
            <?php
        } ?>
        <?= AdminLink::tag($section); ?>
    </section>
    <?php
}
