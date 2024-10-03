<?php

declare(strict_types=1);

namespace app\models;

use davidhirtz\yii2\media\models\traits\MetaImageTrait;

class Asset extends \davidhirtz\yii2\cms\models\Asset
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
}
