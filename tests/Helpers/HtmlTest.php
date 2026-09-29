<?php

declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\Html;
use App\Tests\TestCase;
use App\Tests\Traits\EntryTrait;

/**
 * Outside a page request an entry's URL names the tenant's host, so these assert on the path; the header test in
 * `SiteControllerTest` covers the relative link a page renders.
 */
final class HtmlTest extends TestCase
{
    use EntryTrait;

    public function testALinkableEntryIsAnAnchorWithItsEncodedName(): void
    {
        $entry = $this->createEntry('Fish & Chips', 'fish-and-chips');

        $html = Html::link($entry, ['class' => 'nav-link']);

        self::assertStringStartsWith('<a class="nav-link" href="', $html);
        self::assertStringContainsString('/fish-and-chips"', $html);
        self::assertStringEndsWith('>Fish &amp; Chips</a>', $html);
    }

    public function testTheTextOptionReplacesTheName(): void
    {
        $entry = $this->createEntry('Imprint', 'imprint');

        self::assertStringEndsWith('>Legal</a>', Html::link($entry, ['text' => 'Legal']));
    }

    /**
     * An entry with neither sections nor subentries has no route, and a link to nowhere would be a 404.
     */
    public function testAnEntryWithoutRouteIsASpan(): void
    {
        $entry = $this->createEntry('Placeholder', 'placeholder', withSection: false);

        self::assertSame('<span>Placeholder</span>', Html::link($entry));
    }

    public function testProseWrapsTheContentAndRevealsIt(): void
    {
        $entry = $this->createEntry(withSection: false);
        $section = $this->createSection($entry);
        $section->content = '<p>Text</p>';

        self::assertSame('<div id="x" class="prose reveal"><p>Text</p></div>', Html::prose($section, ['id' => 'x']));
    }

    public function testProseRendersNothingWithoutContent(): void
    {
        $section = $this->createSection($this->createEntry(withSection: false));

        self::assertSame('', Html::prose($section));
    }

    public function testNl2brWritesHtml5Breaks(): void
    {
        self::assertSame("a<br>\nb", Html::nl2br("a\nb"));
    }
}
