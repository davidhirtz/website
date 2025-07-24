<?php

declare(strict_types=1);

namespace app\widgets;

use app\helpers\Html;
use app\models\Asset;
use app\models\Section;

/**
 * @template-extends \davidhirtz\yii2\cms\widgets\Gallery<Asset>
 */
final class Gallery extends \davidhirtz\yii2\cms\widgets\Gallery
{
    public ?Section $section = null;
    public array $canvasOptions = [];

    private static int $count = 0;

    public function init(): void
    {
        $this->assets ??= $this->section?->getVisibleAssets() ?? [];

        $this->viewports = [
            'sm:hidden' => [Asset::TYPE_DEFAULT, Asset::TYPE_VIEWPORT_MOBILE],
            'hidden sm:block' => [Asset::TYPE_DEFAULT, Asset::TYPE_VIEWPORT_DESKTOP]
        ];

        parent::init();
    }

    public function run(): string
    {
        return $this->assets
            ? parent::run()
            : '';
    }

    protected function renderAssetsInternal(array $assets): string
    {
        $content = array_reduce(
            $assets,
            fn($carry, $asset) => $carry . $this->renderCanvas($asset, $this->canvasOptions),
            ''
        );

        return $this->options ? Html::tag('div', $content, $this->options) : $content;
    }

    protected function renderCanvas(Asset $asset, array $options): string
    {
        $options['pictureOptions']['imgOptions']['loading'] ??= self::$count > 3 ? 'lazy' : false;

        if (self::$count > 3 && self::$count < 5) {
            $options['pictureOptions']['imgOptions']['fetchpriority'] ??= 'high';
        }

        self::$count++;

        return Canvas::widget([
            ...$options,
            'asset' => $asset,
        ]);
    }
}
