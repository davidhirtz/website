<?php

declare(strict_types=1);

namespace app\controllers;

use davidhirtz\yii2\skeleton\controllers\traits\AjaxRouteTrait;
use davidhirtz\yii2\skeleton\filters\PageCache;
use davidhirtz\yii2\skeleton\web\ErrorAction;
use davidhirtz\yii2\skeleton\web\View;
use Override;
use Yii;
use yii\helpers\Json;

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
    public function init(): void
    {
        parent::init();

        if (!$this->request->getIsAjaxRoute()) {
            $this->getView()->on(View::EVENT_BEFORE_RENDER, $this->onBeforeRender(...));
        }
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

    private function onBeforeRender(): void
    {
        $url = Yii::$app->get('vite')->getScriptUrl('/resources/js/app.ts');

        $arguments = implode(',', array_map(Json::htmlEncode(...), [
            Yii::$app->params['cookieDomain'] ?? null,
            Yii::$app->params['gtagId'] ?? null,
        ]));

        $this->getView()->registerJs("import i from \"$url\"; i($arguments);", View::POS_MODULE, 'main');
    }
}
