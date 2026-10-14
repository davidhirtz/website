# Deployment

[← Documentation](README.md)

A push to GitHub deploys a branch to the server it is configured for. The server pulls the commit and runs the
migrations itself, so there is no build or upload step — which is why `web/dist` is committed.

## How a push deploys

`.github/workflows/deploy.yml` has two jobs:

1. **check** runs `vendor/bin/check` (code style, static analysis, tests) against a MySQL service. It runs on every
   push to every branch.
2. **deploy** waits for it, and runs only for a branch with a deploy path. It connects to the server over SSH and runs,
   in the deploy path:

   ```bash
   php yii maintenance/enable
   git pull
   composer install --optimize-autoloader --no-dev
   php yii migrate --interactive=0
   php yii cache/flush-all --interactive=0
   php yii registry/push        # optional, see below
   php yii maintenance/disable
   ```

   While maintenance mode is on, the site answers with `runtime/maintenance.php`. If a step fails, the site **stays**
   in maintenance mode: fix the cause on the server, then run `php yii maintenance/disable`.

## Configuring a repository

In the repository's **Settings → Secrets and variables → Actions**:

| Variable               | Kind     | What it does                                                                  |
|------------------------|----------|-------------------------------------------------------------------------------|
| `DEPLOY_PATH_<branch>` | variable | the project folder on the server, e.g. `DEPLOY_PATH_main`; unset, the branch does not deploy |
| `SSH_HOST`             | variable | the server                                                                    |
| `SSH_USERNAME`         | variable | the user the deploy connects as                                               |
| `SSH_KEY`              | secret   | that user's private key                                                       |
| `PHP`                  | variable | the PHP binary on the server, when it is not `php`                            |

A `composer.phar` in the deploy path is used when there is one, the global `composer` otherwise. A typical setup
deploys `main` to the live site and a `stage` branch to a `draft.` subdomain. Only a branch whose name consists of letters,
digits and underscores can have a deploy path, since GitHub allows nothing else in a variable name.

## Setting up a server

Once per server and branch:

```bash
git clone git@github.com:OWNER/REPOSITORY.git /var/www/example
cd /var/www/example
composer install --optimize-autoloader --no-dev
php yii params          # cookie validation key and password pepper
php yii migrate         # asks for the database credentials and writes config/db.php
php yii user/create
```

The server's user needs read access to the repository, usually with a
[deploy key](https://docs.github.com/en/authentication/connecting-to-github-with-ssh/managing-deploy-keys). Point
the web server at `web/` (see [installation](installation.md#web-server)), sign in at `/admin` and open
`/admin/cms/setup` once.

Set what differs on the server in `config/params.php`, e.g. `mailerDsn`, `email`, `cookieDomain` and `gtagId`.

## Cron jobs

```cron
# Deletes cookie consent records older than the configured lifetime
0 3 * * * cd /var/www/example && php yii consent/clear
```

See [cookie consent](cookie-consent.md) for how long they are kept.

## Version registry (optional)

An installation can report itself to a version registry: `php yii registry/push` posts what the admin's
*System → Application* tab shows — name, version, commit, installed extensions, PHP, database, Yii and the
migration state — to `params.registryUrl`, authenticated with `params.registryKey`. `php yii registry/show` prints
the same report without sending it.

```bash
php yii params/create registryUrl https://REGISTRY-HOST/registry/push
php yii params/create registryKey <the key>
```

Most installations don't need this. Without both parameters, the command only warns and exits successfully, as it
does when the registry is down or the key was revoked — which is why the deploy can run it inside the maintenance
window. `--strict` turns these warnings into failures.
