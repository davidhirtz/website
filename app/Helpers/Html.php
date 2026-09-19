<?php

declare(strict_types=1);

namespace App\Helpers;

use Hirtz\Cms\Models\Entry;
use Hirtz\Skeleton\Models\Interfaces\VisibleAttributeInterface;
use yii\helpers\ArrayHelper;

final class Html extends \Hirtz\Media\Helpers\Html
{
    /**
     * @param array<string, mixed> $options
     */
    public static function link(Entry $model, array $options = []): string
    {
        $text = ArrayHelper::remove($options, 'text', $model->getI18nAttribute('name'));
        $text = self::encode($text);
        $route = $model->getRoute();

        return $route ? self::a($text, $route, $options) : self::tag('span', $text, $options);
    }

    public static function nl2br(string $text): string
    {
        return nl2br($text, false);
    }

    /**
     * The content is a custom attribute a type may hide and a model may not declare at all, so it is read through
     * `getVisibleAttribute()` rather than as a property.
     *
     * @param array<string, mixed> $options
     */
    public static function prose(VisibleAttributeInterface $model, array $options = []): string
    {
        $content = $model->getVisibleAttribute('content');

        if (!is_string($content) || $content === '') {
            return '';
        }

        self::addCssClass($options, 'prose');
        self::addDefaultAnimation($options);

        return self::tag('div', $content, $options);
    }

    /**
     * The by-ref parameter cannot be narrower than what `addCssClass()` writes back into the caller's variable.
     *
     * @param array<array-key, mixed> $options
     */
    private static function addDefaultAnimation(array &$options): void
    {
        if (!array_key_exists('data-animation', $options)) {
            self::addCssClass($options, 'animate observe');
            $options['data-animation'] = 'fade-in';
        }
    }
}
