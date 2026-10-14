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
  `/` is a 404 until someone signed in opens `/admin/cms/setup`.
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
npm run build                        # rebuild web/dist after any CSS, JS or new Tailwind class
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
| the pages `/admin/cms/setup` creates | `app/Modules/Admin/Controllers/SetupController.php`                      |
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
- Use the theme tokens (`p-sm`, `gap-md`, `text-h3`, the `xs`–`xl` breakpoints) before arbitrary values.
- `web/dist` is committed and the deploy does not build: run `npm run build` and commit it with the change.
- Match the surrounding code: comments explain *why*, in full sentences, and docblocks name the `@var` of every view
  variable.

## Pitfalls

- Tailwind only generates classes it finds as complete strings in `app/`, `resources/views/` and `resources/js/`.
- `.reveal` (added by `Html::prose()`) fades content in when it scrolls into view; content inside a closed
  `<details>` or a hidden element never does — render it without `Html::prose()` there.
- An entry's `slug` lives in the permalink table, not on `entry`: you cannot query `Entry::find()->where(['slug' => …])`.
  Load the entries and compare `$entry->slug`.
- `/admin/cms/setup` only creates entries while there are none.
- In tests, `FunctionalTestTrait::open()` reuses the application. After `Yii::$app->runAction()` of an action that
  redirects, reset the response (`Yii::$app->set('response', Yii::$app->getComponents()['response'])`) before
  `open()`, or the next page is the redirect.
- A test that attaches the same file twice to one record fails validation; the media `FileFixture` has files 1–6.
- `composer check` must pass before a push: the pre-push hook and GitHub's `check` job run it, and a failing check
  blocks the deploy.
