<?php

declare(strict_types=1);

namespace App\Tests\Modules\Admin\Controllers;

use App\Models\Entry;
use App\Models\Section;
use App\Tests\TestCase;
use Hirtz\Skeleton\Test\Traits\UserFixtureTrait;
use Yii;
use yii\web\Response;

/**
 * Proves the container definitions of `config/prod.php` resolve: the admin renders the project's grid, form rows
 * and section types rather than the bundle's.
 */
final class CmsControllerTest extends TestCase
{
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

    public function testSetupCreatesTheIndexEntry(): void
    {
        $this->login();

        $response = Yii::$app->runAction('admin/cms/setup/index');

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals(1, Entry::find()->count());
        self::assertSame(Entry::getModule()->entryIndexSlug, Entry::find()->one()?->slug);
    }

    private function login(): void
    {
        $this->getWebUser()->setIdentity($this->getUserFromFixture('owner'));
    }

    private function createEntry(string $name = 'Homepage'): Entry
    {
        $entry = Entry::create();
        $entry->name = $name;
        $entry->slug = (string)Entry::getModule()->entryIndexSlug;

        self::assertTrue($entry->insert(), implode(' ', $entry->getFirstErrors()));

        return $entry;
    }

    private function createSection(Entry $entry): Section
    {
        $section = Section::instantiateByType(Section::TYPE_FULL_WIDTH);
        $section->name = 'Intro';
        $section->populateEntryRelation($entry);

        self::assertTrue($section->insert(), implode(' ', $section->getFirstErrors()));

        return $section;
    }
}
