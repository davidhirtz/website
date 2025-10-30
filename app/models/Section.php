<?php

declare(strict_types=1);

namespace app\models;

use Yii;

/**
 * @property-read Entry $entry {@see static::getEntry()}
 * @property-read Entry[] $entries {@see static::getEntries()}
 */
final class Section extends \davidhirtz\yii2\cms\models\Section
{
    public const int TYPE_COLUMN = self::TYPE_DEFAULT;
    public const int TYPE_FULL_WIDTH = 2;

    public static function getTypes(): array
    {
        return [
            self::TYPE_COLUMN => [
                'name' => Yii::t('app', 'Column'),
                'cssClass' => 'relative grid gap-sm w-full sm:w-1/2',
                'hiddenFields' => [],
                'sizes' => '(max-width:767px) 100vw, 50vw',
                'transformations' => [],
            ],
            self::TYPE_FULL_WIDTH => [
                'name' => Yii::t('app', 'Full width'),
                'cssClass' => 'relative grid gap-sm w-full',
                'hiddenFields' => [],
                'sizes' => '100vw',
                'transformations' => [],
            ],
        ];
    }
}
