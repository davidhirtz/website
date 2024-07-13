<?php
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
        <section class="<?= $section->getCssClass(); ?> animate" id="<?= $section->getHtmlId(); ?>" data-animation="fade-in">
            <?= Gallery::widget(['section' => $section]); ?>
            <?php if ($content = $section->getVisibleAttribute('content')) {
                ?>
                <div class="prose">
                    <?= $content; ?>
                </div>
                <?php
            } ?>
            <?= AdminLink::tag($section); ?>
        </section>
        <?php
    } ?>
</div>