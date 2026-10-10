<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\TestCase;
use App\Tests\Traits\EntryTrait;
use Hirtz\Skeleton\Consent\ConsentManager;
use Hirtz\Skeleton\Modules\Admin\Module;
use Hirtz\Skeleton\Test\Traits\FunctionalTestTrait;
use Yii;

final class ConsentTest extends TestCase
{
    use EntryTrait;
    use FunctionalTestTrait;

    public function testTheBannerListsTheServicesAndLinksTheLegalPages(): void
    {
        $this->createEntry();
        $this->open('/');

        self::assertSelectorExists('cc-modal input[data-categories="required"][disabled]');
        self::assertSelectorExists('cc-modal input[data-categories="external"]');
        self::assertAnySelectorTextSame('cc-modal code', '_cc');
        self::assertAnySelectorTextSame('cc-modal dd', 'YouTube');
        self::assertSelectorExists('cc-modal a[href="/privacy"]');
        self::assertSelectorExists('cc-modal a[href="/imprint"]');
        self::assertSelectorExists('footer cc-settings');
    }

    public function testAnalyticsIsNotOfferedWithoutATagId(): void
    {
        $this->createEntry();
        $this->open('/');

        self::assertSelectorNotExists('cc-modal input[data-categories="analytics"]');
    }

    public function testATagManagerContainerIsListedWithAnalytics(): void
    {
        Yii::$app->params['gtagId'] = 'GTM-TEST';

        $this->createEntry();
        $this->open('/');

        self::assertSelectorExists('cc-modal input[data-categories="analytics"]');
        self::assertAnySelectorTextSame('cc-modal dd', 'Google Tag Manager');
        self::assertAnySelectorTextSame('cc-modal dd', 'Google Analytics');
    }

    public function testTheScriptIsHandedTheVersionAndTheLogUrl(): void
    {
        $config = ConsentManager::current()->getClientConfig();

        self::assertMatchesRegularExpression('/^1-[0-9a-f]{8}$/', $config['version']);
        self::assertSame('/application-consent', $config['logUrl']);
        self::assertSame([], $config['cookies']['external']);
        self::assertContains('_cc', $config['declared']);

        $module = Yii::$app->getModule('admin');
        self::assertInstanceOf(Module::class, $module);
        $module->enableConsentLog = false;

        self::assertNull(ConsentManager::current()->getClientConfig()['logUrl']);
    }
}
