<?php

declare(strict_types=1);

namespace App\Controllers;

use davidhirtz\yii2\vite\components\Vite;
use Hirtz\Skeleton\Controllers\Traits\AjaxRouteTrait;
use Hirtz\Tenant\Filters\PageCache;
use Hirtz\Skeleton\Web\Application;
use Hirtz\Skeleton\Web\ErrorAction;
use Override;
use Yii;

final class SiteController extends \Hirtz\Cms\Controllers\SiteController
{
    use AjaxRouteTrait;

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
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
        Application::current()->getErrorHandler()->errorAction = 'site/error';

        $this->request->enableCsrfValidation = false;
        $this->request->enableCsrfCookie = false;

        $this->spacelessOutput = true;

        return parent::beforeAction($action);
    }

    /**
     * @param string $content
     */
    #[Override]
    public function renderContent($content): string
    {
        if ($this->request->getIsAjaxRoute()) {
            return $this->renderAjaxRouteContent($content);
        }

        /** @var Vite $vite */
        $vite = Yii::$app->get('vite');

        $this->getView()->registerJsModule($vite->getScriptUrl('/resources/js/app.ts'), [
            Yii::$app->params['cookieDomain'] ?? null,
            Yii::$app->params['gtagId'] ?? null,
        ]);

        return parent::renderContent($content);
    }
}
