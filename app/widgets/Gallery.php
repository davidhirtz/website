<?php

namespace app\widgets;

use app\models\Asset;
use app\models\Section;

/**
 * @template-extends \davidhirtz\yii2\cms\widgets\Gallery<Asset>
 */
class Gallery extends \davidhirtz\yii2\cms\widgets\Gallery
{
    public Section $section;

    public array $canvasOptions = [];
    public ?int $layout = null;

    public function init(): void
    {
        $this->assets ??= $this->section->getVisibleAssets();

        $this->viewports = [
            'sm:hidden' => [Asset::TYPE_DEFAULT, Asset::TYPE_VIEWPORT_MOBILE],
            'hidden sm:block' => [Asset::TYPE_DEFAULT, Asset::TYPE_VIEWPORT_DESKTOP]
        ];

        $this->canvasOptions['pictureOptions']['imgOptions']['loading'] ??= $this->section->position > 3
            ? 'lazy'
            : 'eager';

        if ($this->section->position < 3) {
            $this->canvasOptions['pictureOptions']['imgOptions']['fetchpriority'] = 'high';
        }

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
        if ($content = $this->returnSingleAsset($assets)) {
            return $content;
        }

        $content = '';

        foreach ($assets as $asset) {
            $content .= $this->renderCanvas($asset, $this->canvasOptions);
        }

        return $content;
    }

    /**
     * @param Asset[] $assets
     */
    private function returnSingleAsset(array $assets): string
    {
        return count($assets) == 1
            ? $this->renderCanvas(current($assets), $this->canvasOptions)
            : '';
    }

    protected function renderCanvas(Asset $asset, array $options): string
    {
        return Canvas::widget([
            ...$options,
            'asset' => $asset,
        ]);
    }
}