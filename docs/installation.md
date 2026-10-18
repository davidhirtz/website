# Installation

[← Documentation](README.md)

## Requirements

- PHP 8.3 or higher with the `exif`, `imagick`, `intl` and `pdo_mysql` extensions (`composer check-platform-reqs` lists
  every one)
- MySQL or MariaDB with InnoDB
- [Composer](https://getcomposer.org/) 2
- Node.js 20.19 or higher, for building the CSS and JavaScript

<!-- template -->
## A new project

```bash
composer create-project davidhirtz/yii2-cms-template my-site
```

The installer asks a few questions, then sets the project up and removes itself:

| Question            | Default                       | What it changes                                                          |
|---------------------|-------------------------------|--------------------------------------------------------------------------|
| Project name        | from the folder name          | the application name in `config/prod.php`, `web/manifest.json`, README  |
| Project slug        | from the name                 | the README's clone instructions, the default database name               |
| Composer package    | `app/<slug>`                  | `name` in `composer.json`                                                |
| License             | `proprietary`                 | anything but `MIT` removes `LICENSE`                                     |
| Preset              | `bare`                        | see below                                                                |
| Database            | `127.0.0.1`, `<slug>`, `root` | `config/db.php`; it creates `<name>` and `<name>_test` when they're missing |
| Migrations          | yes                           | migrates both databases                                                  |
| First user          | yes, when interactive         | runs `php yii user/create`                                               |
| Node dependencies   | yes, when `npm` is installed  | runs `npm install`, and `npm run build` for a preset                     |
| Git repository      | yes                           | `git init`, the pre-push hook, one commit named "Initial commit"         |
| Git remote          | none                          | adds `origin`                                                            |

It also generates `config/params.php` (cookie validation key and password pepper) and `config/local.php`.

### Presets

- **bare** — the layout, the cookie banner and two text section types ("Column", "Full width"). For a project that
  brings its own design and content model.
- **starter** — nine more section types with a view each: hero banner, image & text, text & image, carousel, gallery,
  quote, call to action, accordion and teasers of linked pages. `php yii setup` creates a home page that shows the
  types that need no images, and the imprint and privacy pages the cookie banner links to.
  See [the content model](content-model.md) for how they work.

A preset is a folder in `installer/presets/` that is copied over the project, so its files are the project's own
afterwards — change them as you like.

### Without questions

Every answer can be given ahead as an environment variable, which is what `--no-interaction` and CI use:

```bash
INSTALL_NAME="My Site" INSTALL_PRESET=starter INSTALL_DB_USER=root INSTALL_DB_PASSWORD=secret \
    composer create-project davidhirtz/yii2-cms-template my-site --no-interaction
```

The variables are `INSTALL_NAME`, `INSTALL_SLUG`, `INSTALL_PACKAGE`, `INSTALL_LICENSE`, `INSTALL_PRESET`,
`INSTALL_DB_HOST` (with an optional `:port`), `INSTALL_DB_NAME`, `INSTALL_DB_USER`, `INSTALL_DB_PASSWORD`,
`INSTALL_GIT_REMOTE`, and `0` or `1` for `INSTALL_MIGRATE`, `INSTALL_USER`, `INSTALL_NPM` and `INSTALL_GIT`.

### When it stops half way

Fix the cause — usually the database credentials — and run it again from the project folder:

```bash
composer run-script post-create-project-cmd
```

Each step leaves alone what an earlier run already did.
<!-- /template -->

## An existing project

```bash
git clone git@github.com:OWNER/REPOSITORY.git
cd REPOSITORY
composer install
composer install-hooks
npm install
```

Then create the configuration and the database as described below. On a server, install with
`composer install --optimize-autoloader --no-dev` instead, and skip `install-hooks` and `npm install`.

## Configuration

Three gitignored files in `config/` hold what differs per machine.

### `config/params.php`

```bash
php yii params
```

creates the file with a cookie validation key and a password pepper, and lists all parameters. Add more with
`php yii params/create <name> <value>`:

```php
<?php
return [
    'cookieValidationKey' => '…',
    'passwordPepper' => '…',
    //'cookieDomain' => '.example.com',
    //'email' => 'hello@example.com',
    //'mailerDsn' => 'smtp://user:password@smtp.example.com:587',
    //'gtagId' => 'GTM-XXXXXXX',
];
```

### `config/db.php`

`php yii migrate` asks for the credentials when the file is missing and writes it:

```php
<?php
return [
    'dsn' => 'mysql:host=127.0.0.1;dbname=DATABASE',
    'username' => 'USER',
    'password' => 'PASSWORD',
];
```

The migrations also create the tenant — the record that owns the site's entries, files and host name. The tests use
a second database, see [development](development.md#tests).

### `config/local.php`

Selected by `YII_ENV=local`. It extends `config/dev.php` with whatever only this machine needs:

```php
<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;

return ArrayHelper::merge(require __DIR__ . '/dev.php', [
]);
```

## The first user and the home page

```bash
php yii user/create
php yii setup
```

`user/create` asks for a name, an email address and a password. The first user owns the installation. `setup` creates
the home page (and, with the starter preset, the demo and legal pages declared in `config/setup.php`), attributed to
the owner or to `--user=<email>`. Until then, `/` answers with a 404. The installer runs both when it migrates.

Once the owner exists, Claude Code in this folder can edit the content through the `cms` server of `.mcp.json`, see
[Agents (MCP)](mcp.md).

## Web server

### PHP's built-in server

For development:

```bash
YII_ENV=local php -S localhost:8080 -t web
```

### Apache

Point the document root at `web/`. `web/.htaccess` routes every request that is not a file to `index.php`, sets
long cache headers for assets, and redirects a bare domain to `https://www.` — `draft.` subdomains, `localhost`,
`127.0.0.1` and `*.test` / `*.localhost` hosts are left alone. Remove those lines for a site served without `www.`.

### nginx

```nginx
server {
    server_name www.example.com;
    root /var/www/example/web;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
}
```

### Permissions

`runtime/`, `web/assets/` and `web/uploads/` must be writable by both the web server and the console user.
`composer install` sets them to `0777`.
