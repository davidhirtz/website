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
    private static int $count = 0;

    #[Override]
    public function init(): void
    {
        if ($cssClass = $this->asset->getCssClass()) {
            Html::addCssClass($this->wrapperOptions, $cssClass);
        }

        $this->pictureOptions['imgOptions']['loading'] ??= self::$count > 3 ? 'lazy' : false;

        if (self::$count < 3) {
            $this->pictureOptions['imgOptions']['fetchpriority'] ??= 'high';
        }

        self::$count++;

        parent::init();
    }
}
