<?php

declare(strict_types=1);

namespace App\Tests\Modules\Admin\Controllers;

use App\Tests\TestCase;
use App\Tests\Traits\EntryTrait;
use Hirtz\Skeleton\Test\Traits\UserFixtureTrait;
use Yii;

/**
 * Proves the container definitions of `config/prod.php` resolve: the admin renders the project's grid, form rows
 * and section types rather than the bundle's.
 */
final class CmsControllerTest extends TestCase
{
    use EntryTrait;
    use UserFixtureTrait;

    public function testTheEntryIndexRendersTheProjectGrid(): void
    {
        $this->login();
        $entry = $this->createEntry();

        $html = Yii::$app->runAction('admin/cms/entry/index');

        self::assertIsString($html);
        self::assertStringContainsString('entry-grid-view', $html);
        self::assertStringContainsString($entry->name, $html);
    }

    public function testTheEntryFormOffersBothMenus(): void
    {
        $this->login();
        $entry = $this->createEntry();

        $html = Yii::$app->runAction('admin/cms/entry/update', ['id' => $entry->id]);

        self::assertIsString($html);
        self::assertStringContainsString('Main menu', $html);
        self::assertStringContainsString('Footer', $html);
    }

    public function testTheSectionFormOffersTheProjectTypes(): void
    {
        $this->login();
        $section = $this->createSection($this->createEntry());

        $html = Yii::$app->runAction('admin/cms/section/update', ['id' => $section->id]);

        self::assertIsString($html);
        self::assertStringContainsString('Column', $html);
        self::assertStringContainsString('Full width', $html);
    }

    private function login(): void
    {
        $this->getWebUser()->setIdentity($this->getUserFromFixture('owner'));
    }
}
