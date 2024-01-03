<?php

namespace app\helpers;

use Yii;
use yii\helpers\Url;

class Html extends \yii\helpers\Html
{
    public static function a($text, $url = null, $options = []): string
    {
        if (!$url) {
            return parent::tag('span', $text, $options);
        }

        if (is_array($url)) {
            $url = Url::toRoute($url);
        }

        $host = trim(parse_url($url, PHP_URL_HOST) ?? '');

        if ((!empty($host) && $host !== Yii::$app->getRequest()->getHostName())) {
            $options['target'] ??= '_blank';
            $options['rel'] ??= 'noopener';
        }

        if (str_contains($url, '/uploads/')) {
            $options['download'] = true;
        }

        return parent::a($text, $url, $options);
    }

    public static function nl2br(string $text): string
    {
        return nl2br($text, false);
    }
}
