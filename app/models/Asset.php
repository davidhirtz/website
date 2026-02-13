<?php

declare(strict_types=1);

namespace app\models;

use davidhirtz\yii2\media\models\traits\MetaImageTrait;
use Override;

final class Asset extends \davidhirtz\yii2\cms\models\Asset
{
    use MetaImageTrait;

    public function getVisibleAttributes(): array
    {
        if ($this->isEntryAsset()) {
            return [
                'status',
                'type',
                'link',
                'alt_text',
            ];
        }

        return [
            'status',
            'type',
            'content',
            'alt_text',
            'link',
        ];
    }

    #[Override]
    public function isAttributeVisible(string $attribute): bool
    {
        return !in_array($attribute, $this->getVisibleAttributes(), true);
    }
}
