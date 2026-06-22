<?php

declare(strict_types=1);

namespace app\controllers;

use davidhirtz\yii2\skeleton\controllers\traits\AjaxRouteTrait;
use davidhirtz\yii2\skeleton\filters\PageCache;
use davidhirtz\yii2\skeleton\web\ErrorAction;
use Override;
use Yii;

final class SiteController extends \davidhirtz\yii2\cms\controllers\SiteController
{
    use AjaxRouteTrait;

    #[Override]
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
                'view' => '@views/site/error.php',
            ],
        ];
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            ...parent::behaviors(),
            'PageCache' => [
                'class' => PageCache::class,
                'params' => [
                    'page',
                    'slug',
                ],
            ],
        ];
    }

    #[Override]
    public function beforeAction($action): bool
    {
        Yii::$app->getErrorHandler()->errorAction = 'site/error';

        $this->request->enableCsrfValidation = false;
        $this->request->enableCsrfCookie = false;

        $this->spacelessOutput = true;

        return parent::beforeAction($action);
    }

    #[Override]
    public function renderContent($content): string
    {
        if ($this->request->getIsAjaxRoute()) {
            return $this->renderAjaxRouteContent($content);
        }

        $this->getView()->registerJsModule(Yii::$app->get('vite')->getScriptUrl('/resources/js/app.ts'), [
            Yii::$app->params['cookieDomain'] ?? null,
            Yii::$app->params['gtagId'] ?? null,
        ]);

        return parent::renderContent($content);
    }
}
