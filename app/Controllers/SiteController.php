<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Consent\Consent;
use Hirtz\Skeleton\Controllers\Traits\AjaxRouteTrait;
use Hirtz\Tenant\Filters\PageCache;
use Hirtz\Skeleton\Web\Application;
use Hirtz\Skeleton\Web\ErrorAction;
use Hirtz\Vite\Vite;
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

        Vite::current()->registerJsModule('/resources/js/app.ts', [
            [
                'cookieDomain' => Yii::$app->params['cookieDomain'] ?? null,
                'tagId' => Yii::$app->params['gtagId'] ?? null,
                'consent' => Consent::getClientConfig(),
            ],
        ]);

        return parent::renderContent($content);
    }
}
