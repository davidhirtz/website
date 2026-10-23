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
composer create-project davidhirtz/website my-site
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

When the migrations are on, it connects to the database right after these questions. If the server refuses, it says
why and asks for other credentials; without a terminal, it skips the migrations, the first user and the pages, and lists
them among the next steps.

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
    composer create-project davidhirtz/website my-site --no-interaction
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

### With Docker

On a machine without PHP, clone this repository instead and run the installer in the container of
[`compose.yaml`](#docker):

```bash
git clone https://github.com/davidhirtz/website.git my-site
cd my-site
docker compose run --rm app sh installer/docker.sh
docker compose up
```

It asks the same questions but the database's, which is the container's, and keeps the clone's git history instead
of making an "Initial commit".
<!-- /template -->

## An existing project

### For development

```bash
git clone git@github.com:OWNER/REPOSITORY.git
cd REPOSITORY
composer install
composer install-hooks
npm install
```

### On a server

The folder usually exists already, created with the virtual host and maybe holding files of its own, so `git clone`
refuses it. Check the repository out into it instead:

```bash
cd /var/www/example
git init
git remote add origin git@github.com:OWNER/REPOSITORY.git
git fetch origin
git checkout --track origin/main
composer install --optimize-autoloader --no-dev
```

Files already in the folder stay untracked, unless the repository has a file of the same name, in which case the
checkout stops: move it out of the way first. Skip `install-hooks` and `npm install`, since `web/dist` is committed.
The server's user needs read access to the repository, see [deployment](deployment.md#setting-up-a-server).

Then, in both cases, create the configuration and the database as described below.

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

Once the owner exists, Claude Code in this folder can edit the content through the `website` server of `.mcp.json`, see
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
# An SVG opened on its own runs its scripts on the site's origin; uploads are sanitised, this is the second line.
# A map rather than an `add_header` in a location, which would drop the server's headers for that location.
map $uri $svg_csp {
    ~*\.svg$ "script-src 'none'";
}

# Redirects HTTP and the bare domain to https://www.
server {
    listen 80;
    listen [::]:80;
    server_name example.com www.example.com;
    return 301 https://www.example.com$request_uri;
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name example.com;
    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;
    return 301 https://www.example.com$request_uri;
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name www.example.com;
    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;

    root /var/www/example/web;
    index index.php;
    charset utf-8;

    # The largest upload the media library accepts, together with PHP's `upload_max_filesize` and `post_max_size`
    client_max_body_size 128M;

    server_tokens off;
    add_header Strict-Transport-Security "max-age=31536000" always;
    add_header X-Content-Type-Options nosniff always;
    add_header Content-Security-Policy $svg_csp always;

    gzip on;
    gzip_types application/javascript application/json application/manifest+json image/svg+xml text/css;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    # Hidden files such as `.htaccess`, but not `.well-known` for Let's Encrypt. Regular expressions are tried in
    # order, so this comes first.
    location ~ /\.(?!well-known) {
        deny all;
    }

    # A changed file gets a new URL, so assets can be cached for a year. A missing image still goes to index.php,
    # which creates the image transformation on its first request.
    location ~* \.(avif|css|gif|ico|jpe?g|js|mp4|otf|png|svg|webp|woff2?)$ {
        expires 1y;
        try_files $uri /index.php$is_args$args;
    }

    # Nothing uploaded or published may run as PHP
    location ~ ^/(assets|uploads)/.*\.php$ {
        deny all;
    }

    location ~ \.php$ {
        try_files $uri =404;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
}
```

Unlike Apache, nginx passes the `Authorization` header to PHP as it is, which the [MCP endpoint](mcp.md) needs. The
application sends its own `Content-Security-Policy` with `frame-ancestors 'self'`, so `X-Frame-Options` is not needed;
`X-XSS-Protection` is ignored by current browsers. For a site served without `www.`, swap the server names of the
redirect and the site. Certbot can add the certificates and the redirects itself (`certbot --nginx`).

### Docker

`compose.yaml` runs the site with PHP 8.5's built-in server and a MariaDB 11.8 server, for trying the project out or working
on it without PHP, Composer and Node.js on the machine:

```bash
docker compose up
```

The first start installs the Composer dependencies, generates `config/params.php`, migrates both databases, creates
the first user and the home page, which takes a few minutes. Then sign in at <http://localhost:8080/admin> as
`admin@example.com` with the password `website123`. Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` before the first start for
other credentials, and `PORT` for another port.

Run any other command in the running container:

```bash
docker compose exec app npm run build
docker compose exec app composer check
docker compose exec app php yii message
```

The project folder is mounted into the container, so changes show without a restart. The database is a volume of its
own, `docker compose down --volumes` removes it. The site's database comes from environment variables that
`config/dev.php` reads ahead of `config/db.php`, which a machine with its own MySQL server can therefore keep.

On Linux, the container writes as the user 1000. Set `DOCKER_UID` and `DOCKER_GID` to the output of `id -u` and
`id -g` when they differ.

### Permissions

`runtime/`, `web/assets/` and `web/uploads/` must be writable by both the web server and the console user.
`composer install` sets them to `0777`.
