<?php

namespace app\models;

use davidhirtz\yii2\skeleton\validators\HtmlValidator;
use Yii;

/**
 * @property-read Entry $entry {@see static::getEntry()}
 * @property-read Entry[] $entries {@see static::getEntries()}
 */
class Section extends \davidhirtz\yii2\cms\models\Section
{
    final public const TYPE_COLUMN = self::TYPE_DEFAULT;
    final public const TYPE_FULL_WIDTH = 2;

    public string|array|null $htmlValidator = [
        HtmlValidator::class,
        'allowedHtmlTags' => ['h1', 'h2', 'h3'],
        'allowedClasses' => ['btn'],
    ];

    public static function getTypes(): array
    {
        return [
            static::TYPE_COLUMN => [
                'name' => Yii::t('app', 'Column'),
                'cssClass' => 'w-12 w-6-sm',
                'hiddenFields' => [],
                'sizes' => [],
                'transformations' => [],
            ],
            static::TYPE_FULL_WIDTH => [
                'name' => Yii::t('app', 'Full width'),
                'cssClass' => 'w-12 w-6-sm',
                'hiddenFields' => [],
                'sizes' => [],
                'transformations' => [],
            ],
        ];
    }
}
