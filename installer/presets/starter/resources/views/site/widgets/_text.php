<?php

declare(strict_types=1);

/**
 * The heading, text and button a section type shares, rendered by the type's own view around its images.
 *
 * @var View $this
 * @var Section $section
 * @var string|null $heading the heading's tag, `h2` unless the view says otherwise
 */

use App\Helpers\Html;
use App\Models\Section;
use Hirtz\Skeleton\Web\View;

$name = $section->getVisibleAttribute('name');
$link = $section->hasButton() ? $section->getVisibleAttribute('link') : null;

if (is_string($name) && $name !== '') {
    echo Html::tag($heading ?? 'h2', Html::encode($name));
}

echo Html::prose($section);

if (is_string($link) && $link !== '') {
    ?>
    <p>
        <?= Html::a(Html::encode($section->getVisibleAttribute('link_text') ?: Yii::t('app', 'SECTION_LINK_TEXT_DEFAULT')), $link, [
            'class' => 'btn',
        ]); ?>
    </p>
    <?php
}
