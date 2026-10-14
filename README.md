README
============================

A website built on [Yii 2](https://www.yiiframework.com/) and [yii2-cms](https://github.com/davidhirtz/yii2-cms): pages
made of typed sections, a media library with responsive images, menus, a cookie consent banner that blocks external
content until it is allowed, and an admin to edit it all.

<!-- template -->
## Creating a project

```bash
composer create-project davidhirtz/yii2-cms-template my-site
```

The installer asks for the project's name, the database and a preset — **bare** for two text section types and
nothing else, or **starter** for nine more (hero banner, image & text, carousel, gallery, quote, call to action,
accordion, teasers) and demo content. It creates the databases, writes the configuration, creates the first user and
makes the "Initial commit". See [installation](docs/installation.md#a-new-project).
<!-- /template -->

## Getting started

Requires PHP 8.3+ with `imagick`, MySQL or MariaDB, Composer and Node.js 20.19+.

```bash
git clone git@github.com:OWNER/REPOSITORY.git
cd REPOSITORY
composer install && composer install-hooks
npm install
php yii params && php yii migrate && php yii user/create
YII_ENV=dev php -S localhost:8080 -t web
```

Sign in at <http://localhost:8080/admin> and open `/admin/cms/setup` once to create the home page.

## Everyday commands

```bash
npm run dev                         # Vite dev server
npm run build                       # build web/dist — commit it, the deploy does not build
composer check                      # code style, static analysis and tests, as CI runs them
composer cs-fix                     # fix the code style
php yii message messages/config.php # update the translation files
```

## Documentation

- [Installation](docs/installation.md)
- [Development](docs/development.md)
- [The content model](docs/content-model.md) — including how to add a section type
- [Deployment](docs/deployment.md)
- [Cookie consent](docs/cookie-consent.md)
- [AGENTS.md](AGENTS.md) — for AI coding agents


License
-------

Released under the [MIT License](LICENSE).
