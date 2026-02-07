<?php

declare(strict_types=1);

namespace app\widgets;

use app\helpers\Html;
use app\models\Asset;
use app\models\Section;
use Override;

/**
 * @template-extends \davidhirtz\yii2\cms\widgets\Gallery<Asset>
 */
final class Gallery extends \davidhirtz\yii2\cms\widgets\Gallery
{
    public ?Section $section = null;
    public array $canvasOptions = [];

    #[Override]
    public function init(): void
    {
        $this->assets ??= $this->section?->getVisibleAssets() ?? [];

        $this->viewports = [
            'sm:hidden' => [Asset::TYPE_DEFAULT, Asset::TYPE_VIEWPORT_MOBILE],
            'hidden sm:block' => [Asset::TYPE_DEFAULT, Asset::TYPE_VIEWPORT_DESKTOP]
        ];

        parent::init();
    }

    #[Override]
    public function run(): string
    {
        return $this->assets
            ? parent::run()
            : '';
    }

    #[Override]
    protected function renderAssetsInternal(array $assets): string
    {
        $content = array_reduce(
            $assets,
            fn ($carry, $asset) => $carry . $this->renderCanvas($asset, $this->canvasOptions),
            ''
        );

        return $this->options ? Html::tag('div', $content, $this->options) : $content;
    }

    protected function renderCanvas(Asset $asset, array $options): string
    {
        return Canvas::widget([
            ...$options,
            'asset' => $asset,
        ]);
    }
}
