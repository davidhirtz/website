<?php

declare(strict_types=1);

namespace app\widgets;

use app\helpers\Html;
use app\models\Asset;

/**
 * @property Asset $asset
 */
class Canvas extends \davidhirtz\yii2\cms\widgets\Canvas
{
    public function init(): void
    {
        if ($cssClass = $this->asset->getCssClass()) {
            Html::addCssClass($this->wrapperOptions, $cssClass);
        }

        parent::init();
    }
}
