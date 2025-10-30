<?php

declare(strict_types=1);

namespace app\helpers;

use app\models\Asset;
use app\models\Entry;
use app\models\Section;
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

    public static function prose(Asset|Entry|Section $model, array $options = []): string
    {
        $content = $model->getI18nAttribute('content');

        if (!$content) {
            return '';
        }

        self::addCssClass($options, 'prose');
        self::addDefaultAnimation($options);

        return self::tag('div', $content, $options);
    }

    private static function addDefaultAnimation(array &$options): void
    {
        if (!array_key_exists('data-animation', $options)) {
            self::addCssClass($options, 'animate observe');
            $options['data-animation'] = 'fade-in';
        }
    }
}
