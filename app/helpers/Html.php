<?php

declare(strict_types=1);

namespace app\helpers;

use app\models\Entry;
use yii\helpers\ArrayHelper;

final class Html extends \davidhirtz\yii2\media\helpers\Html
{
    public static function link(Entry $model, array $options = []): string
    {
        $text = ArrayHelper::remove($options, 'text', $model->getI18nAttribute('name'));
        return self::a(self::encode($text), $model->getRoute(), $options);
    }

    public static function nl2br(string $text): string
    {
        return nl2br($text, false);
    }
}
