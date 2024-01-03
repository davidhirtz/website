<?php

namespace app\controllers;

use davidhirtz\yii2\skeleton\controllers\traits\AjaxRouteTrait;
use Yii;

class SiteController extends \davidhirtz\yii2\cms\controllers\SiteController
{
    use AjaxRouteTrait;

    public bool $spacelessOutput = true;

    public function init(): void
    {
        Yii::$app->getErrorHandler()->errorAction = 'site/error';
        Yii::$app->getRequest()->enableCsrfValidation = false;

        parent::init();
    }

    public function actionError(): string
    {
        if (Yii::$app->getRequest()->getIsAjax() && !Yii::$app->getRequest()->getIsAjaxRoute()) {
            return Yii::$app->getErrorHandler()->exception->getMessage();
        }

        return $this->render('error');
    }
}
