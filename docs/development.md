# Development

[← Documentation](README.md)

## Structure

```
app/                  the project's PHP code, namespace App\ (models, controllers, admin widgets, helpers)
config/               prod.php, dev.php and test.php are committed; db.php, params.php and local.php are not
messages/             translations, one folder per language
resources/css/        Tailwind CSS: base styles, components, utilities
resources/js/         TypeScript: the entry point app.ts, custom elements, the cookie consent
resources/views/      the frontend views: layouts/main.php, site/_sections.php and one view per section type
tests/                PHPUnit tests, run against a database of their own
web/                  the document root: index.php, the built assets in dist/, uploads/
```

Most of what the project does comes from its dependencies, which are configured rather than copied:

| Package                                                          | What it brings                                                     |
|------------------------------------------------------------------|--------------------------------------------------------------------|
| [yii2-skeleton](https://github.com/davidhirtz/yii2-skeleton)     | the application, users, the admin shell, cookie consent, console commands |
| [yii2-cms](https://github.com/davidhirtz/yii2-cms)               | entries, sections, categories, menus, the site controller          |
| [yii2-media](https://github.com/davidhirtz/yii2-media)           | files, folders, assets, image transformations                      |
| [yii2-anakin](https://github.com/davidhirtz/yii2-anakin)         | the admin theme                                                    |
| [yii2-vite](https://github.com/davidhirtz/yii2-vite)             | the Vite manifest and dev server integration                       |
| [yii2-dev](https://github.com/davidhirtz/yii2-dev)               | the code style, static analysis and test configuration (`require-dev`) |

A project changes a bundle's class by replacing it in the container: `config/prod.php` maps
`Hirtz\Cms\Models\Section` to `App\Models\Section`, for instance, so everything that creates a section — admin,
site, tests — gets the project's.

## Environments

`YII_ENV` selects `config/<env>.php` for both `web/index.php` and `yii`:

| `YII_ENV`        | Config         | Debug | Use                                                      |
|------------------|----------------|-------|----------------------------------------------------------|
| `prod` (default) | `prod.php`     | off   | servers                                                  |
| `dev`            | `dev.php`      | on    | development; mail goes to `runtime/mail` instead of out  |
| `local`          | `local.php`    | on    | development, with this machine's own additions           |
| `test`           | `test.php`     | on    | the tests                                                |

```bash
YII_ENV=dev php yii <command>
```

## CSS and JavaScript

[Vite](https://vite.dev/) builds `resources/js/app.ts` and the CSS it imports into `web/dist/`:

```bash
npm run dev     # the Vite dev server, with hot reload
npm run build   # type-check and build for production
```

**`web/dist` is committed, and the deploy does not build.** Commit it with every CSS or JavaScript change, or the
server keeps the old files.

The CSS is [Tailwind 4](https://tailwindcss.com/). `resources/css/base/theme.css` holds the breakpoints, spacing and
type scale; `config/prod.php` repeats the breakpoints in pixels for the image `sizes`, so change both together.
Tailwind finds its classes in `app/`, `resources/js/` and `resources/views/` — a class built from strings it cannot
see (`'w-' . $size`) is not generated.

The frontend scripts are custom elements (`x-toggle`, `x-carousel`, `cc-modal`, …), so markup that contains one works
wherever it is rendered, including content loaded by the ajax router.

## Checks

A change has to pass the code style ([PHP CS Fixer](https://cs.symfony.com/)), static analysis
([PHPStan](https://phpstan.org/)) and the tests ([PHPUnit](https://phpunit.de/)):

```bash
composer check      # all three, as the pre-push hook and CI run them
composer cs-fix     # fix the code style
composer phpstan
composer test
```

`composer check` only reports; `composer cs-fix` rewrites. The rules come from `yii2-dev`, so a `composer update`
updates them in every project; `.php-cs-fixer.php`, `phpstan.neon` and `rector.php` only list the folders to scan.

`composer install-hooks` enables the committed pre-push hook once per clone. GitHub runs the same checks on every
push, and a deploy waits for them.

## Tests

The tests run against their own database, `cms_template_test`, configured in `config/test.php`. They use the
credentials of `config/db.php` when there is one, else `MYSQL_DSN`, `MYSQL_USER` and `MYSQL_PASSWORD`. Create it
once; `composer check` migrates it on every run.

```bash
YII_ENV=test php yii migrate --interactive=0
```

`tests/Functional/SiteControllerTest.php` shows how to render a page and query it with CSS selectors;
`tests/Traits/EntryTrait.php` creates entries and sections.

## Translations

Messages are addressed by an `UPPER_SNAKE_CASE` key in the `app` category, never by their English text:

```php
Yii::t('app', 'SECTION_TYPE_HERO')
```

The key and category must be string literals in the `Yii::t()` call itself. A key reached through a variable or a
ternary is invisible to the extractor, which **deletes it from every language file** on its next run.

```bash
php yii message messages/config.php
```

adds new keys to `messages/de/app.php` and `messages/en-US/app.php` and removes unused ones. Add a language in
`messages/config.php` and in the `i18n` configuration.
