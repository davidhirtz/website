<?php

namespace app\controllers;

use davidhirtz\yii2\skeleton\controllers\traits\AjaxRouteTrait;
use davidhirtz\yii2\skeleton\filters\PageCache;
use davidhirtz\yii2\skeleton\web\ErrorAction;
use Yii;

class SiteController extends \davidhirtz\yii2\cms\controllers\SiteController
{
    use AjaxRouteTrait;

    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
                'view' => '@views/site/error.php',
            ],
        ];
    }

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'PageCache' => [
                'class' => PageCache::class,
                'params' => ['slug'],
            ],
        ]);
    }

    public function init(): void
    {
        if (!Yii::$app->getRequest()->getIsConsoleRequest()) {
            Yii::$app->getErrorHandler()->errorAction = 'site/error';
            Yii::$app->getRequest()->enableCsrfValidation = false;
        }

        $this->spacelessOutput = true;

        parent::init();
    }
}
