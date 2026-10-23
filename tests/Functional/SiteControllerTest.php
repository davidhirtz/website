<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Models\Entry;
use App\Models\Menus\SiteMenu;
use App\Models\Section;
use App\Models\SectionAsset;
use App\Tests\TestCase;
use App\Tests\Traits\EntryTrait;
use Hirtz\Media\Test\Fixtures\FileFixture;
use Hirtz\Media\Test\Fixtures\FolderFixture;
use Hirtz\Skeleton\Test\Browser;
use Hirtz\Skeleton\Test\Traits\FunctionalTestTrait;

/**
 * The smoke test of the whole frontend stack: the url rules, `SiteController`, `layouts/main`, `site/view`,
 * `site/_sections` and the Vite manifest.
 */
final class SiteControllerTest extends TestCase
{
    use EntryTrait;
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

    public function testTheIndexWithoutAnEntryLinksToTheAdmin(): void
    {
        $this->open('/');

        self::assertResponseStatusCodeSame(404);
        self::assertSelectorExists('main section.p-md .prose a[href="/admin/cms/entry/index"]');
    }

    public function testTheIndexEntryIsServedByTheProjectLayout(): void
    {
        $entry = $this->createEntry();

        $this->open('/');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame($entry->name);
        self::assertSelectorExists('main section');
        self::assertSelectorExists('script[type="module"]');
        self::assertSelectorExists('cc-modal');
    }

    public function testTheMetaTagsComeFromTheEntry(): void
    {
        $entry = $this->createEntry();
        $entry->title = 'The document title';
        $entry->description = 'The meta description';

        self::assertNotFalse($entry->update(), implode(' ', $entry->getFirstErrors()));

        $this->open('/');

        self::assertPageTitleSame('The document title');
        self::assertSame('The meta description', self::$crawler->filter('meta[name="description"]')->attr('content'));
        // Absolute URLs name the tenant's host, not the request's.
        self::assertMatchesRegularExpression('#^https://[^/]+/$#', (string)self::$crawler->filter('link[rel="canonical"]')->attr('href'));
    }

    public function testAnEntryInTheMainMenuIsLinkedFromTheHeader(): void
    {
        $this->createEntry();
        $entry = $this->createEntry('Imprint', 'imprint', SiteMenu::Main);

        $this->open('/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('header nav li a', $entry->name);
        self::assertSame('/imprint', self::$crawler->filter('header nav li a')->attr('href'));
        self::assertSelectorNotExists('footer nav a');
    }

    public function testAnEntryInTheFooterMenuIsLinkedFromTheFooter(): void
    {
        $this->createEntry();
        $entry = $this->createEntry('Privacy', 'privacy', SiteMenu::Footer);

        $this->open('/');

        self::assertSelectorTextSame('footer nav li a', $entry->name);
        self::assertSelectorNotExists('header nav a');
    }

    public function testASubentryOfAMenuEntryIsNestedBelowIt(): void
    {
        $this->createEntry();
        $parent = $this->createEntry('About', 'about', SiteMenu::Main);
        $child = $this->createEntry('Team', 'team', SiteMenu::Main, $parent);

        $this->open('/');

        self::assertSelectorTextSame('header nav > ul > li > a', $parent->name);
        self::assertSelectorTextSame('header nav li ul li a', $child->name);
    }

    /**
     * Both section types declare the same `wrapper()`, so consecutive sections share one flex row.
     */
    public function testConsecutiveSectionsShareOneWrapper(): void
    {
        $entry = $this->createEntry(withSection: false);
        $first = $this->createSection($entry, 'Left', Section::TYPE_COLUMN);
        $second = $this->createSection($entry, 'Right', Section::TYPE_COLUMN);

        $this->open('/');

        self::assertResponseIsSuccessful();
        self::assertCount(1, self::$crawler->filter('main .flex.flex-wrap'));
        self::assertCount(2, self::$crawler->filter('main .flex.flex-wrap > section'));
        self::assertSelectorExists("section#{$first->getHtmlId()}");
        self::assertSelectorExists("section#{$second->getHtmlId()}");
    }

    public function testTheSectionContentIsRenderedAsProse(): void
    {
        $entry = $this->createEntry(withSection: false);
        $section = $this->createSection($entry);
        $section->content = '<p>The section content</p>';

        self::assertNotFalse($section->update(), implode(' ', $section->getFirstErrors()));

        $this->open('/');

        self::assertSelectorTextSame('main section .prose.reveal p', 'The section content');
    }

    public function testASectionAssetIsRenderedAsAnArtwork(): void
    {
        $section = $this->createEntry()->sections[0] ?? null;
        self::assertInstanceOf(Section::class, $section);

        $this->createSectionAsset($section, [
            'alt_text' => 'The alt text',
            'link' => 'https://www.example.com',
        ]);

        $this->open('/');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('main section img[alt="The alt text"]');
        self::assertSelectorExists('main section a[href="https://www.example.com"]');
    }

    public function testAnEmbedWaitsForTheCookieConsent(): void
    {
        $section = $this->createEntry()->sections[0] ?? null;
        self::assertInstanceOf(Section::class, $section);

        $this->createSectionAsset($section, ['embed_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);

        $this->open('/');

        self::assertSelectorExists('main section [data-embed] button[data-embed-src]');
        self::assertSelectorExists('main section [data-embed] cc-button[data-add-categories="required,external"]');
        self::assertSelectorExists('main section [data-embed] iframe[hidden]');
    }

    /**
     * An ajax route answers the page content alone, which the frontend swaps into the current document.
     */
    public function testAnAjaxRouteRendersTheContentWithoutTheLayout(): void
    {
        $this->createEntry();

        self::$client = new Browser([
            'HTTP_HOST' => 'www.test.localhost',
            'HTTPS' => 'on',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            'HTTP_X_AJAX_REQUEST' => 'route',
        ]);

        self::$crawler = self::$client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('section');
        self::assertSelectorNotExists('header');
        self::assertSelectorNotExists('cc-modal');

        $cacheControl = self::$client->getResponse()->getHeader('Cache-Control');
        self::assertIsString($cacheControl);
        self::assertStringContainsString('no-store', $cacheControl);
    }

    public function testAnUnknownSlugRendersTheProjectErrorPage(): void
    {
        $this->open('/there-is-no-such-entry');

        self::assertResponseStatusCodeSame(404);
        self::assertSelectorExists('header nav');
        self::assertSelectorExists('main section.p-md .prose');
    }

    public function testADisabledEntryIsNotFound(): void
    {
        $this->createEntry();
        $entry = $this->createEntry('Draft', 'draft');
        $entry->status = Entry::STATUS_DISABLED;

        self::assertNotFalse($entry->update(), implode(' ', $entry->getFirstErrors()));

        $this->open('/draft');

        self::assertResponseStatusCodeSame(404);
    }

    public function testTheSitemapListsTheEntries(): void
    {
        $this->createEntry();
        $this->createEntry('Imprint', 'imprint');

        $this->open('/sitemap.xml');

        self::assertResponseIsSuccessful();
        self::assertMatchesRegularExpression(
            '#<loc>https://[^/<]+/imprint</loc>#',
            self::$client->getResponse()->getContent(),
        );
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function createSectionAsset(Section $section, array $attributes): SectionAsset
    {
        $asset = SectionAsset::create();
        $asset->model_id = $section->id;
        $asset->file_id = 1;
        $asset->setAttributes($attributes, false);

        self::assertTrue($asset->insert(), implode(' ', $asset->getFirstErrors()));

        return $asset;
    }
}
