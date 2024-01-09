<?php

namespace app\controllers;

use davidhirtz\yii2\skeleton\controllers\traits\AjaxRouteTrait;
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

    public function init(): void
    {
        Yii::$app->getRequest()->enableCsrfValidation = false;
        $this->spacelessOutput = true;

        parent::init();
    }
}
