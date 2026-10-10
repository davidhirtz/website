<?php

declare(strict_types=1);

namespace App\Consent;

use Hirtz\Skeleton\Helpers\Url;
use Hirtz\Skeleton\Modules\Admin\Module;
use Yii;

/**
 * The services the cookie banner asks for, rendered by `layouts/_cc` and handed to `consent.ts`. A project adds or
 * removes services here and raises {@see Consent::VERSION} whenever it does, so every visitor is asked again.
 */
final class Consent
{
    /**
     * Logged with every decision: the version of this list a visitor agreed to.
     */
    public const string VERSION = '2026-10-10';

    public const string PRIVACY_URL = '/privacy';
    public const string IMPRINT_URL = '/imprint';

    /**
     * @return list<Category>
     */
    public static function getCategories(): array
    {
        return array_values(array_filter([
            self::getRequiredCategory(),
            self::getAnalyticsCategory(),
            self::getExternalCategory(),
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public static function getClientConfig(): array
    {
        $cookies = [];

        foreach (self::getCategories() as $category) {
            if (!$category->required) {
                $cookies[$category->id] = $category->getFirstPartyCookieNames();
            }
        }

        return [
            'version' => self::VERSION,
            'logUrl' => Module::current()->enableConsentLog ? Url::to(['/consent/create']) : null,
            'cookies' => $cookies,
            'declared' => array_merge(...array_map(
                fn (Category $category): array => $category->getFirstPartyCookieNames(),
                self::getCategories(),
            )),
            'debug' => YII_DEBUG,
        ];
    }

    private static function getRequiredCategory(): Category
    {
        return new Category(
            id: 'required',
            title: Yii::t('app', 'CC_CATEGORY_REQUIRED_TITLE'),
            description: Yii::t('app', 'CC_CATEGORY_REQUIRED_TEXT'),
            services: [
                new Service(
                    name: Yii::t('app', 'CC_SERVICE_CONSENT_NAME'),
                    provider: Yii::$app->name,
                    purpose: Yii::t('app', 'CC_SERVICE_CONSENT_PURPOSE'),
                    privacyUrl: self::PRIVACY_URL,
                    cookies: [
                        new Cookie('_cc', Yii::t('app', 'CC_DURATION_YEARS', ['n' => 1])),
                    ],
                ),
            ],
            required: true,
        );
    }

    /**
     * Offered only with a tag ID: a Google Tag Manager container (`GTM-…`) or a Google Analytics stream (`G-…`).
     * Whatever else a container loads must be listed here too.
     */
    private static function getAnalyticsCategory(): ?Category
    {
        $tagId = Yii::$app->params['gtagId'] ?? null;

        if (!is_string($tagId) || $tagId === '') {
            return null;
        }

        $services = [];

        if (str_starts_with($tagId, 'GTM-')) {
            $services[] = new Service(
                name: 'Google Tag Manager',
                provider: 'Google Ireland Limited',
                purpose: Yii::t('app', 'CC_SERVICE_GTM_PURPOSE'),
                privacyUrl: 'https://policies.google.com/privacy',
            );
        }

        $services[] = new Service(
            name: 'Google Analytics',
            provider: 'Google Ireland Limited',
            purpose: Yii::t('app', 'CC_SERVICE_GA_PURPOSE'),
            privacyUrl: 'https://policies.google.com/privacy',
            cookies: [
                new Cookie('_ga', Yii::t('app', 'CC_DURATION_YEARS', ['n' => 2])),
                new Cookie('_ga_*', Yii::t('app', 'CC_DURATION_YEARS', ['n' => 2])),
            ],
        );

        return new Category(
            id: 'analytics',
            title: Yii::t('app', 'CC_CATEGORY_ANALYTICS_TITLE'),
            description: Yii::t('app', 'CC_CATEGORY_ANALYTICS_TEXT'),
            services: $services,
        );
    }

    /**
     * Embedded players, booking forms or podcasts: each provider whose iframe the site embeds belongs here.
     */
    private static function getExternalCategory(): Category
    {
        return new Category(
            id: 'external',
            title: Yii::t('app', 'CC_CATEGORY_EXTERNAL_TITLE'),
            description: Yii::t('app', 'CC_CATEGORY_EXTERNAL_TEXT'),
            services: [
                new Service(
                    name: 'YouTube',
                    provider: 'Google Ireland Limited',
                    purpose: Yii::t('app', 'CC_SERVICE_YOUTUBE_PURPOSE'),
                    privacyUrl: 'https://policies.google.com/privacy',
                    cookies: [
                        new Cookie('YSC', Yii::t('app', 'CC_DURATION_SESSION'), true),
                        new Cookie('VISITOR_INFO1_LIVE', Yii::t('app', 'CC_DURATION_MONTHS', ['n' => 6]), true),
                        new Cookie('VISITOR_PRIVACY_METADATA', Yii::t('app', 'CC_DURATION_MONTHS', ['n' => 6]), true),
                    ],
                ),
                new Service(
                    name: 'Vimeo',
                    provider: 'Vimeo.com, Inc.',
                    purpose: Yii::t('app', 'CC_SERVICE_VIMEO_PURPOSE'),
                    privacyUrl: 'https://vimeo.com/privacy',
                    cookies: [
                        new Cookie('vuid', Yii::t('app', 'CC_DURATION_YEARS', ['n' => 2]), true),
                    ],
                ),
            ],
        );
    }
}
