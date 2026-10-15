# AGENTS.md

Guidance for AI coding agents working in this repository. People should start with the [README](README.md) and
[docs/](docs/README.md); this file is the condensed version, plus the pitfalls.

A Yii 2 website on [yii2-cms](https://github.com/davidhirtz/yii2-cms): pages (entries) made of typed sections, a
media library, menus, cookie consent and an admin at `/admin`. PHP 8.3, MySQL/MariaDB, Tailwind 4, TypeScript, Vite.

<!-- template -->
## Creating a project from this template

Run the installer without questions; it renames the project, writes `config/db.php`, `params.php` and `local.php`,
creates and migrates `<db>` and `<db>_test`, builds the assets and commits everything as "Initial commit":

```bash
INSTALL_NAME="Project Name" INSTALL_PRESET=starter \
INSTALL_DB_USER=… INSTALL_DB_PASSWORD=… INSTALL_GIT_REMOTE=git@github.com:OWNER/REPOSITORY.git \
    composer create-project davidhirtz/yii2-cms-template project-folder --no-interaction
```

- Ask the user for the preset (`bare` or `starter`), the database credentials and the git remote rather than guessing.
  Pass credentials through the environment only; never write them into committed files or repeat them back.
- `INSTALL_USER` defaults to off without a terminal. Create the first user afterwards with
  `YII_USER_PASSWORD=… php yii user/create --name=… --email=… --interactive=0`, or leave it to the user.
- The new project has no template history and no template remote. Don't push unless asked.
- Then verify: `composer check` passes, and `YII_ENV=local php -S localhost:8080 -t web` serves `/admin`.
  `/` is a 404 until `php yii setup` has run (the installer runs it after migrating).
- All variables are listed in [docs/installation.md](docs/installation.md#without-questions).

Changes to the installer itself live in `installer/Installer.php`; a preset is a folder in `installer/presets/` copied
over the project. `.github/workflows/installer.yml` creates a project for each preset on every push and runs its
checks — the way to test an installer change locally is the same `create-project` with
`--repository='{"type":"path","url":"<template copy without vendor>","options":{"symlink":false,"versions":{"davidhirtz/yii2-cms-template":"dev-main"}}}'`
into a scratch folder.
<!-- /template -->

## Commands

```bash
composer check                       # the gate: code style, PHPStan, tests — run before every commit
composer cs-fix                      # fix the code style
npm run build                        # optimize web/images/**/*.svg, rebuild web/dist after any CSS, JS or new Tailwind class
php yii message messages/config.php  # sync translation keys into messages/*/app.php
YII_ENV=test php yii migrate --interactive=0   # after adding a migration (check also does this)
YII_ENV=local php -S localhost:8080 -t web     # serve the site
```

## Where to change what

| To change…                         | Edit                                                                       |
|------------------------------------|----------------------------------------------------------------------------|
| section types and their fields     | `app/Models/Section.php` (`getTypes()`, `getDefaultCustomAttributes()`)    |
| how a section type renders         | `resources/views/site/_<view>.php`, named by the type's `viewFile()`        |
| the page layout, menus, footer     | `resources/views/layouts/main.php`                                         |
| which bundle class is replaced     | `container.definitions` in `config/prod.php`                               |
| module options, menus, image sizes | `modules` in `config/prod.php`                                             |
| the pages `php yii setup` creates  | `config/setup.php` (without it, a home page)                               |
| colors, spacing, breakpoints       | `resources/css/base/theme.css` (breakpoints also in `config/prod.php`)     |
| cookie categories and services     | `components.consent` in `config/prod.php`                                  |
| UI texts                           | `messages/en-US/app.php` and `messages/de/app.php`                         |

[docs/content-model.md](docs/content-model.md#adding-a-section-type) walks through adding a section type.

## Conventions

- Every PHP file starts with `declare(strict_types=1);`. Classes are `final` unless meant to be extended, and
  overrides carry `#[Override]`.
- Extend a bundle class in `app/` and map it in `config/prod.php`'s container definitions; never edit `vendor/`.
- Section type numbers and menu values are stored in the database: add new ones, never renumber or reuse.
- A custom attribute (an extra field) needs no migration, but a `@property` line on the model for PHPStan.
- Texts: `Yii::t('app', 'UPPER_SNAKE_KEY')` with **literal** category and key. A key built from a variable or a
  ternary is invisible to the extractor, which then deletes it from every language file. Translate every new key in
  both `de` and `en-US`.
- In views, read fields with `$model->getVisibleAttribute('name')`, encode plain text with `Html::encode()`, and
  output `content` (sanitized HTML) as is. Add `AdminLink::tag($section)` to each section's element.
- `web/dist` is committed and the deploy does not build: run `npm run build` and commit it with the change.
- Match the surrounding code: comments explain *why*, in full sentences, and docblocks name the `@var` of every view
  variable.

## Styling

- Style with Tailwind classes in the markup: layout, spacing, typography and colors (`block`, `w-full`, `flex`,
  `absolute`, `gap-md`, …). Fall back to a file in `resources/css/components/` only for what classes express badly:
  elements with animated states, transitions, `@starting-style`, `:popover-open` and the like. Component CSS is
  unlayered and beats any utility, so never set one property of an element in both places.
- Never use `@apply`. CSS spells out its declarations and reads the theme through its variables
  (`gap: var(--spacing-xs)`, `color: var(--color-white)`); a hover variant becomes
  `@media (hover: hover) { &:hover { … } }`.
- Use the spacing tokens of `resources/css/base/theme.css` (`p-sm`, `gap-md`, `mt-lg`, …). When a value in the design
  is close to a token, use the token rather than the exact pixel value. The same goes for the text sizes (`text-h3`)
  and the `xs`–`xl` breakpoints.
- A distance that belongs to the page rather than to one element — to the page edge, the header height — gets a
  named spacing token in `@theme` (`--spacing-page: 3.5rem` makes `px-page`, `top-page`), which says more than
  `top-[30px]`. Add missing colors, font weights and text sizes to `@theme` the same way instead of repeating an
  arbitrary value.
- Sizes are in `rem` (1rem = 16px), with the pixel value as a comment in `theme.css`; arbitrary values too
  (`w-[20.625rem]`, not `w-[330px]`). Only 1px borders stay in `px`.

## Pitfalls

- Tailwind only generates classes it finds as complete strings in `app/`, `resources/views/` and `resources/js/`.
- `.reveal` (added by `Html::prose()`) fades its children in when they scroll into view. They are visible without
  JavaScript, and the script only hides what is below the viewport on load, so never set `opacity: 0` on them
  yourself. `data-reveal-group` and `data-reveal-delay` on the container give it its own queue and timing
  ([docs/development.md](docs/development.md#reveal)).
- An entry's `slug` lives in the permalink table, not on `entry`: you cannot query `Entry::find()->where(['slug' => …])`.
  Load the entries and compare `$entry->slug`.
- `npm run build` rewrites the SVGs in `web/images` with svgo. An SVG whose animation svgo breaks goes into `exclude`
  in `resources/scripts/svgo.js`.
- `php yii setup` only creates entries while there are none.
- In tests, `FunctionalTestTrait::open()` reuses the application. After `Yii::$app->runAction()` of an action that
  redirects, reset the response (`Yii::$app->set('response', Yii::$app->getComponents()['response'])`) before
  `open()`, or the next page is the redirect.
- A test that attaches the same file twice to one record fails validation; the media `FileFixture` has files 1–6.
- `composer check` must pass before a push: the pre-push hook and GitHub's `check` job run it, and a failing check
  blocks the deploy.
