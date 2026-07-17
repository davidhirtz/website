<?php

declare(strict_types=1);

namespace app\models;

use Override;
use Yii;

/**
 * @property-read Asset[] $assets
 * @property-read Entry $entry
 * @property-read Entry[] $entries
 */
final class Section extends \davidhirtz\yii2\cms\models\Section
{
    public const int TYPE_COLUMN = self::TYPE_DEFAULT;
    public const int TYPE_FULL_WIDTH = 2;

    #[Override]
    public static function getTypes(): array
    {
        return [
            self::TYPE_COLUMN => [
                'name' => Yii::t('app', 'SECTION_TYPE_COLUMN'),
                'cssClass' => 'relative grid gap-sm w-full sm:w-1/2',
                'hiddenFields' => [],
                'sizes' => '(max-width:767px) 100vw, 50vw',
            ],
            self::TYPE_FULL_WIDTH => [
                'name' => Yii::t('app', 'SECTION_TYPE_FULL_WIDTH'),
                'cssClass' => 'relative grid gap-sm w-full',
                'hiddenFields' => [],
                'sizes' => '100vw',
                'transformations' => [],
            ],
        ];
    }
}
