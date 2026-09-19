<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Models\Entry;
use App\Models\Menus\SiteMenu;
use App\Models\Section;
use App\Models\SectionAsset;
use App\Tests\TestCase;
use Hirtz\Media\Test\Fixtures\FileFixture;
use Hirtz\Media\Test\Fixtures\FolderFixture;
use Hirtz\Skeleton\Test\Traits\FunctionalTestTrait;

/**
 * The smoke test of the whole frontend stack: the url rules, `SiteController`, `layouts/main`, `site/view`,
 * `site/_sections` and the Vite manifest.
 */
final class SiteControllerTest extends TestCase
{
    use FunctionalTestTrait;

    /**
     * @return array<string, mixed>
     */
    public function fixtures(): array
    {
        return [
            'folder' => FolderFixture::class,
            'file' => FileFixture::class,
        ];
    }

    public function testTheIndexEntryIsServedByTheProjectLayout(): void
    {
        $entry = $this->createEntry('Homepage', (string)Entry::getModule()->entryIndexSlug);

        $this->open('/');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame($entry->name);
        self::assertSelectorExists('main section');
        self::assertSelectorExists('script[type="module"]');
    }

    public function testAnEntryInTheMainMenuIsLinkedFromTheHeader(): void
    {
        $this->createEntry('Homepage', (string)Entry::getModule()->entryIndexSlug);
        $entry = $this->createEntry('Imprint', 'imprint', SiteMenu::Main);

        $this->open('/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('header nav li a', $entry->name);
        self::assertSame('/imprint', self::$crawler->filter('header nav li a')->attr('href'));
    }

    public function testASectionAssetIsRenderedAsAnArtwork(): void
    {
        $entry = $this->createEntry('Homepage', (string)Entry::getModule()->entryIndexSlug);
        $section = $entry->sections[0] ?? null;

        self::assertInstanceOf(Section::class, $section);

        $asset = SectionAsset::create();
        $asset->model_id = $section->id;
        $asset->file_id = 1;
        $asset->alt_text = 'The alt text';
        $asset->link = 'https://www.example.com';

        self::assertTrue($asset->insert(), implode(' ', $asset->getFirstErrors()));

        $this->open('/');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('main section img[alt="The alt text"]');
        self::assertSelectorExists('main section a[href="https://www.example.com"]');
    }

    public function testAnUnknownSlugIsNotFound(): void
    {
        $this->open('/there-is-no-such-entry');

        self::assertResponseStatusCodeSame(404);
    }

    /**
     * An entry with neither sections nor subentries has no route at all, so the section is what makes it linkable.
     */
    private function createEntry(string $name, string $slug, ?SiteMenu $menu = null): Entry
    {
        $entry = Entry::create();
        $entry->name = $name;
        $entry->slug = $slug;
        $entry->menu_ids = $menu ? [$menu->value] : null;

        self::assertTrue($entry->insert(), implode(' ', $entry->getFirstErrors()));

        $section = Section::instantiateByType(Section::TYPE_FULL_WIDTH);
        $section->name = "$name section";
        $section->populateEntryRelation($entry);

        self::assertTrue($section->insert(), implode(' ', $section->getFirstErrors()));
        $entry->populateSectionRelations([$section]);

        return $entry;
    }
}
