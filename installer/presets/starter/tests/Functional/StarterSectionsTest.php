<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Models\Section;
use App\Models\SectionAsset;
use App\Tests\TestCase;
use App\Tests\Traits\EntryTrait;
use Hirtz\Cms\Models\SectionEntry;
use Hirtz\Media\Test\Fixtures\FileFixture;
use Hirtz\Media\Test\Fixtures\FolderFixture;
use Hirtz\Skeleton\Test\Fixtures\UserFixture;
use Hirtz\Skeleton\Test\Traits\FunctionalTestTrait;
use Hirtz\Skeleton\Test\Traits\UserFixtureTrait;
use Yii;

/**
 * The section types of the starter preset, each through its own view in `resources/views/site`, and the demo
 * content `/admin/cms/setup` creates with them.
 */
final class StarterSectionsTest extends TestCase
{
    use EntryTrait;
    use FunctionalTestTrait;
    use UserFixtureTrait;

    /**
     * @return array<string, mixed>
     */
    public function fixtures(): array
    {
        return [
            'user' => UserFixture::class,
            'folder' => FolderFixture::class,
            'file' => FileFixture::class,
        ];
    }

    public function testSetupCreatesTheDemoHomePageAndTheLegalPages(): void
    {
        $this->getWebUser()->setIdentity($this->getUserFromFixture('owner'));
        Yii::$app->runAction('admin/cms/setup/index');

        // The setup answers with a redirect, which the next request would otherwise send again.
        Yii::$app->set('response', Yii::$app->getComponents()['response']);
        $this->getWebUser()->setIdentity(null);

        $this->open('/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('main section h1', 'Welcome to');
        self::assertSelectorExists('main section a.btn[href="#sections"]');
        self::assertSelectorExists('main section#sections');
        self::assertSelectorExists('main figure blockquote');
        self::assertCount(2, self::$crawler->filter('main details summary'));
        self::assertSelectorExists('footer nav a[href="/imprint"]');
        self::assertSelectorExists('footer nav a[href="/privacy"]');

        $this->open('/privacy');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('main section h2', 'Privacy policy');
    }

    public function testTheHeroCoversItselfWithItsFirstImage(): void
    {
        $section = $this->createSectionOfType(Section::TYPE_HERO);
        $this->createSectionAsset($section);

        $this->open('/');

        self::assertSelectorExists('main section img.object-cover');
        self::assertSelectorTextSame('main section h1', 'Heading');
    }

    public function testTheImageOfTextAndImageComesLast(): void
    {
        $section = $this->createSectionOfType(Section::TYPE_TEXT_IMAGE);
        $this->createSectionAsset($section);

        $this->open('/');

        self::assertSelectorExists('main section > .sm\:order-last img');
    }

    public function testTheCarouselHasOneSlidePerImage(): void
    {
        $section = $this->createSectionOfType(Section::TYPE_CAROUSEL);
        $this->createSectionAsset($section);
        $this->createSectionAsset($section, 2);

        $this->open('/');

        self::assertCount(2, self::$crawler->filter('main x-carousel [data-track] > div'));
        self::assertSelectorExists('main x-carousel button[data-next]');
    }

    public function testConsecutiveAccordionSectionsFormOneList(): void
    {
        $entry = $this->createEntry(withSection: false);
        $this->createSection($entry, 'First question', Section::TYPE_ACCORDION);
        $this->createSection($entry, 'Second question', Section::TYPE_ACCORDION);

        $this->open('/');

        self::assertCount(1, self::$crawler->filter('main div.divide-y'));
        self::assertCount(2, self::$crawler->filter('main div.divide-y > details'));
    }

    public function testTeasersLinkTheLinkedEntries(): void
    {
        $section = $this->createSectionOfType(Section::TYPE_TEASERS);
        $linked = $this->createEntry('Linked page', 'linked-page');

        $relation = SectionEntry::create();
        $relation->populateModelRelation($section);
        $relation->entry_id = $linked->id;

        self::assertTrue($relation->insert(), implode(' ', $relation->getFirstErrors()));

        $this->open('/');

        self::assertSelectorTextSame('main section li h3 a', 'Linked page');
        self::assertSame('/linked-page', self::$crawler->filter('main section li h3 a')->attr('href'));
    }

    /**
     * Only the types listed in `Section::TYPES_WITH_BUTTON` have a button, so the others do not even accept one.
     */
    public function testATextSectionHasNoButton(): void
    {
        $section = $this->createSectionOfType(Section::TYPE_FULL_WIDTH);
        $section->setAttributes(['link' => '/somewhere']);

        self::assertFalse($section->hasButton());
        self::assertFalse($section->isAttributeSafe('link'));

        $this->open('/');

        self::assertSelectorNotExists('main a.btn');
    }

    private function createSectionOfType(int $type): Section
    {
        $section = $this->createSection($this->createEntry(withSection: false), 'Heading', $type);

        if ($section->hasButton()) {
            $section->link = '/elsewhere';
            self::assertNotFalse($section->update(), implode(' ', $section->getFirstErrors()));
        }

        return $section;
    }

    private function createSectionAsset(Section $section, int $fileId = 1): SectionAsset
    {
        $asset = SectionAsset::create();
        $asset->model_id = $section->id;
        $asset->file_id = $fileId;

        self::assertTrue($asset->insert(), implode(' ', $asset->getFirstErrors()));

        return $asset;
    }
}
