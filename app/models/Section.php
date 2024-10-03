<?php

declare(strict_types=1);

namespace app\models;

use Yii;

/**
 * @property-read Entry $entry {@see static::getEntry()}
 * @property-read Entry[] $entries {@see static::getEntries()}
 */
class Section extends \davidhirtz\yii2\cms\models\Section
{
    final public const TYPE_COLUMN = self::TYPE_DEFAULT;
    final public const TYPE_FULL_WIDTH = 2;

    public static function getTypes(): array
    {
        return [
            self::TYPE_COLUMN => [
                'name' => Yii::t('app', 'Column'),
                'cssClass' => 'w-full sm:w-1/2',
                'hiddenFields' => [],
                'sizes' => [],
                'transformations' => [],
            ],
            self::TYPE_FULL_WIDTH => [
                'name' => Yii::t('app', 'Full width'),
                'cssClass' => 'w-full',
                'hiddenFields' => [],
                'sizes' => [],
                'transformations' => [],
            ],
        ];
    }
}
