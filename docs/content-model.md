# The content model

[← Documentation](README.md)

## Entries and sections

An **entry** is a page: it has a name, a slug (its URL), a title and a description for search engines, and it can
be in a menu. An entry's content is a stack of **sections**. Each section has a **type** that decides which fields
the admin offers and how the site renders it.

```
Entry "About"  →  /about
├── Section  type: Hero banner     name, content, button, 1 image
├── Section  type: Image & text    name, content, button, 1 image
└── Section  type: Accordion       name, content
```

Images are **assets**: a file from the media library attached to an entry or a section, with its own alt text,
caption and link. A section can also link other entries, which the starter's "Teasers" type renders as cards.

## Section types

The types are declared in `App\Models\Section::getTypes()`:

```php
SectionType::make(self::TYPE_FULL_WIDTH)
    ->name(Yii::t('app', 'SECTION_TYPE_FULL_WIDTH'))   // the name in the admin's type list
    ->cssClass('grid gap-sm w-full')                   // the section element's classes
    ->wrapper('flex flex-wrap justify-center')         // see "Grouping" below
    ->sizes('100vw')                                   // the images' `sizes` attribute
    ->allowEntries(false),                             // no "Entries" tab in the admin
```

| Option                         | What it does                                                                      |
|--------------------------------|-----------------------------------------------------------------------------------|
| `name()`                       | the label in the admin                                                            |
| `viewFile()`                   | the view in `resources/views/site` that renders it; `_sections` by default        |
| `cssClass()`                   | classes for `$section->getCssClass()`                                             |
| `sizes()`                      | the `sizes` of its images, e.g. `Size::breakpoint('sm', '100vw'), '50vw'`         |
| `transformations()`            | which image sizes to generate, see "Images" below                                 |
| `allowAssets(false)`           | no images                                                                         |
| `allowEntries(false)`          | no linked entries                                                                 |
| `hiddenFields('content', …)`   | hides fields of the section form                                                  |
| `wrapper()`, `group()`, `collect()` | see "Grouping" below                                                         |
| `visible(false)`               | keeps it out of the site, while the admin still edits it                          |

### Adding a section type

As an example, a "Logos" type that shows the section's images in a row:

1. **Declare it** in `app/Models/Section.php`:

   ```php
   public const int TYPE_LOGOS = 12;

   // in getTypes()
   SectionType::make(self::TYPE_LOGOS)
       ->name(Yii::t('app', 'SECTION_TYPE_LOGOS'))
       ->viewFile('_logos')
       ->sizes('160px')
       ->allowEntries(false),
   ```

   The number is what the database stores, so never reuse or renumber one.

2. **Render it** in `resources/views/site/_logos.php`. A type's view gets the consecutive sections of that type as
   `$sections`:

   ```php
   <?php

   declare(strict_types=1);

   /**
    * @var Hirtz\Skeleton\Web\View $this
    * @var App\Models\Section[] $sections
    */

   use Hirtz\Cms\Widgets\Gallery;
   use Hirtz\Skeleton\Widgets\AdminLink;

   foreach ($sections as $section) {
       ?>
       <section class="relative py-lg" id="<?= $section->getHtmlId(); ?>">
           <?= Gallery::make()
               ->assets($section->getVisibleAssets())
               ->attributes(['class' => 'flex flex-wrap items-center justify-center gap-lg']); ?>
           <?= AdminLink::tag($section); ?>
       </section>
       <?php
   }
   ```

   `AdminLink::tag()` adds the "edit" link signed-in editors see on the site.

3. **Name it**: `php yii message messages/config.php` adds `SECTION_TYPE_LOGOS` to the language files; fill in
   the translations.

4. **Test it**: render a page with such a section, as `tests/Functional/SiteControllerTest.php` does, and assert
   the markup.

5. **Build**: `npm run build`, if the view uses Tailwind classes that weren't used anywhere before.

### Fields of their own

A section has three fields: `name` (the heading), `content` (rich text) and `slug` (its HTML id). More fields are
**custom attributes**, declared in `getDefaultCustomAttributes()` and stored in one JSON column, so they need no
migration:

```php
protected function getDefaultCustomAttributes(): array
{
    return [
        ...parent::getDefaultCustomAttributes(),
        UrlCustomAttribute::make('link')
            ->label(Yii::t('app', 'SECTION_LINK_LABEL'))
            ->relative()
            ->visible(static fn (self $section): bool => $section->hasButton()),
    ];
}
```

`visible()` decides per section whether the field exists at all: an invisible field has no form field and no
validation rule, and a value sent for it is ignored. The starter preset uses this to give only four of its types a
button. Declare each custom attribute as `@property` on the class, so static analysis knows it.

The skeleton has `TextCustomAttribute`, `HtmlCustomAttribute`, `UrlCustomAttribute`, `SelectCustomAttribute`,
`BooleanCustomAttribute`, `NumberCustomAttribute`, `DateCustomAttribute`, `HexColorCustomAttribute` and others; see
`vendor/davidhirtz/yii2-skeleton/src/Models/CustomAttributes`.

In a view, read a field with `$section->getVisibleAttribute('link')`, which is `null` when the type hides it with
`hiddenFields()`. For a field with a `visible()` closure, check the same condition in the view, as the starter's
`widgets/_text.php` does with `hasButton()`.

### Grouping

The site renders an entry's sections in runs: consecutive sections with the same view file are one **group** and are
passed to that view together. That is how the starter's `_accordion.php` puts consecutive accordion sections into one
list.

- `wrapper('…')` puts consecutive groups whose types share the same wrapper classes into one element. The "Column"
  and "Full width" types share `flex flex-wrap justify-center`, so two columns sit side by side.
- `group('…')` sets the group key explicitly, to keep apart sections that share a view, or to join ones that don't.
- `collect(SectionStack::collectAdjacent())` and `collect(SectionStack::collectAll())` pull further sections into
  a head section's group.

## Images

Uploaded files live in the media library; a section or entry links them as assets. The site renders an asset with
`Hirtz\Cms\Widgets\Artwork` (one image) or `Gallery` (several), as `<picture>` with AVIF and WebP sources.

Responsive images need **transformations**, the scaled copies the server generates on the first request. Declare
them in `config/prod.php`:

```php
use Hirtz\Media\Transformations\Transformation;

'media' => [
    'transformations' => [
        Transformation::make('sm')->width(768),
        Transformation::make('lg')->width(1280),
        Transformation::make('xl')->width(1920),
    ],
],
```

A type can name the ones its images use with `->transformations('sm', 'lg')` and tells the browser how wide they are
displayed with `->sizes()`. The `media.breakpoints` in `config/prod.php` are what `Size::breakpoint()` resolves, and
must match the Tailwind breakpoints in `resources/css/base/theme.css`.

## Menus

Two menus are declared in `config/prod.php` and `App\Models\Menus\SiteMenu`: **Main** (the header) and **Footer**.
An entry's form has a checkbox for each. `layouts/main.php` renders them with `MenuCollection::getRootItems()` and
`getItems()`. Their values are stored on the entries, so don't renumber them.

## Setup

`php yii setup` inserts the entries of `config/setup.php` while there are none yet, each with optional `sections` and
subentries (`entries`); without the file, it inserts a home page. Delete the file once every server has run it:

```php
return [
    'entries' => fn (): array => [
        [
            'name' => 'Imprint',
            'slug' => 'imprint',
            'menu_ids' => [SiteMenu::Footer->value],
            'sections' => [
                ['type' => Section::TYPE_FULL_WIDTH, 'name' => 'Imprint', 'content' => '<p>…</p>'],
            ],
        ],
    ],
];
```

## The rest of the admin

Entries can have types of their own, categories, subentries and blocks (sections reused across pages); all of that is
configured on `App\Models\Entry` and the `cms` module in `config/prod.php`, and described in the
[yii2-cms](https://github.com/davidhirtz/yii2-cms) documentation and its `UPGRADE.md`.
