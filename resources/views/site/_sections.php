<?php

declare(strict_types=1);

/**
 * @see Section::TYPE_DEFAULT
 *
 * @var View $this
 * @var Section[] $sections
 */

use app\models\Section;
use app\widgets\Gallery;
use davidhirtz\yii2\cms\widgets\AdminLink;
use davidhirtz\yii2\skeleton\web\View;

?>
<div class="wrap flex flex-wrap justify-center">
    <?php
    foreach ($sections as $section) {
        ?>
        <section class="<?= $section->getCssClass(); ?>" id="<?= $section->getHtmlId(); ?>">
            <?= Gallery::widget(['section' => $section]); ?>
            <?php if ($content = $section->getVisibleAttribute('content')) {
                ?>
                <div class="prose animate observe" data-animation="fade-in">
                    <?= $content; ?>
                </div>
                <?php
            } ?>
            <?= AdminLink::tag($section); ?>
        </section>
        <?php
    } ?>
</div>