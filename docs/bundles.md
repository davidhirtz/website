# Optional bundles

[← Documentation](README.md)

A few bundles add what not every site needs. Each one bootstraps itself: after `composer require` it registers its
module, its admin pages, its migrations and its [MCP](mcp.md) resources, so there is nothing to add to
`config/prod.php` before it works. Commit the changed `composer.json` and `composer.lock`; the deploy runs the
migrations. Credentials go into the gitignored `config/params.php`, on every machine and on the server, with
`php yii params/create <name> <value>`.

| Bundle                                                                          | Adds                                                     |
|---------------------------------------------------------------------------------|----------------------------------------------------------|
| [yii2-shopify](https://github.com/davidhirtz/yii2-shopify)                      | the products of a Shopify store, kept in sync            |
| [yii2-cms-shopify](https://github.com/davidhirtz/yii2-cms-shopify)              | an entry per product                                     |
| [yii2-location](https://github.com/davidhirtz/yii2-location)                    | locations with address and coordinates, a JSON/GeoJSON API |
| [yii2-location-google](https://github.com/davidhirtz/yii2-location-google)      | Google Places search in the location form                |

The bundles' READMEs describe every option; this page covers getting each one running in this project.

## Shopify

```bash
composer require davidhirtz/yii2-shopify davidhirtz/yii2-cms-shopify
php yii migrate
```

yii2-shopify mirrors the store's products into the database and shows them in the admin under *Products*; it never
writes to Shopify. yii2-cms-shopify lets an entry stand for one product, picked in the entry form, so a product page
is an entry with sections like any other. Leave it out when the site only lists products.

In the Shopify admin, create a custom app under *Apps → Develop apps* with the Admin API scopes `read_products` and
`read_inventory`. Its *API credentials* page has everything the site needs:

```bash
php yii params/create shopifyShopName my-shop          # from https://my-shop.myshopify.com
php yii params/create shopifyAccessToken shpat_…       # the Admin API access token, shown only once
php yii params/create shopifyApiSecret …               # verifies the webhooks
php yii shopify/import                                 # the first import
```

Webhooks keep the products in sync from then on. They need a public URL, so install them on the live site, under
*Products → Webhooks → Install Webhooks* in the admin. *Reload Products* on the product index, or
`php yii shopify/import`, fixes a sync that looks wrong.

For the entry's product, use the trait on the project's entry and declare the column for PHPStan:

```php
use Hirtz\Shopify\Models\Traits\ProductRelationTrait;

/**
 * @property int|null $product_id
 */
final class Entry extends \Hirtz\Cms\Models\Entry
{
    use ProductRelationTrait;
}
```

The site preloads an entry's product with its default variant, so a view reads them without extra queries:

```php
<?php if ($product = $entry->product) { ?>
    <p><?= Html::encode($product->name) ?>, <?= $product->variant?->getFormattedPrice() ?></p>
<?php } ?>
```

## Locations

```bash
composer require davidhirtz/yii2-location
php yii migrate
```

The admin gets *Locations*, and `/api/location/index.json` (or `.geojson`) lists the enabled ones for a map. Declare
the types of locations, their translated attributes and custom fields on the container in `config/prod.php`, and
turn on tags under `modules.location.enableTags` to group them; the
[README](https://github.com/davidhirtz/yii2-location#configuration) has an example.

With yii2-location-google, the location form searches Google Places and fills in the address, name and coordinates
of the place picked. Enable the *Places API (New)* in the Google Cloud Console and create an API key for it:

```bash
composer require davidhirtz/yii2-location-google
php yii params/create googleApiKey …
```

Without the key the bundle does nothing, and the form keeps its plain fields.

## With an agent

Each bundle adds its records to the site's MCP server: `product` (read-only, since the products come from
Shopify), `location` and `tag`. An agent can then create locations or link entries to products the same way it
edits pages.
