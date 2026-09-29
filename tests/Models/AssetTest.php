<?php

declare(strict_types=1);

namespace App\Tests\Models;

use App\Models\EntryAsset;
use App\Models\SectionAsset;
use App\Tests\TestCase;
use Hirtz\Skeleton\Models\CustomAttributes\CustomAttribute;

/**
 * A definition left out is neither rendered in the admin nor saved, so these lists are what an editor can fill in.
 */
final class AssetTest extends TestCase
{
    public function testAnEntryAssetCarriesAnAltTextAndALink(): void
    {
        self::assertSame(['alt_text', 'link'], $this->getCustomAttributeNames(EntryAsset::create()));
    }

    public function testASectionAssetAddsACaptionAndAnEmbed(): void
    {
        self::assertSame(['content', 'alt_text', 'link', 'embed_url'], $this->getCustomAttributeNames(SectionAsset::create()));
    }

    /**
     * @return list<string>
     */
    private function getCustomAttributeNames(EntryAsset|SectionAsset $asset): array
    {
        return array_map(static fn (CustomAttribute $definition): string => $definition->name, $asset->getCustomAttributes());
    }
}
