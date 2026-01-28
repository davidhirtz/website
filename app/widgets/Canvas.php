<?php

declare(strict_types=1);

namespace app\widgets;

use app\helpers\Html;
use app\models\Asset;
use Override;

/**
 * @property Asset $asset
 */
final class Canvas extends \davidhirtz\yii2\cms\widgets\Canvas
{
    public array $wrapperOptions = ['class' => 'canvas relative'];
    private static int $counter = 0;

    #[Override]
    public function init(): void
    {
        if ($cssClass = $this->asset->getCssClass()) {
            Html::addCssClass($this->wrapperOptions, $cssClass);
        }

        $this->pictureOptions['imgOptions']['loading'] ??= self::$counter++ > 3 ? 'lazy' : 'eager';

        if (self::$counter < 3) {
            $this->pictureOptions['imgOptions']['fetchpriority'] ??= 'high';
        }

        parent::init();
    }
}
