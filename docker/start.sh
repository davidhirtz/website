#!/bin/sh
# Prepares the project in the container and serves it. Each step leaves alone what an earlier start already did, so
# only the first start installs, migrates and creates the first user and the home page.
set -e

[ -f vendor/autoload.php ] || composer install --no-interaction --no-progress

# Its listing of all parameters includes the secrets it generates.
[ -f config/params.php ] || php yii params --interactive=0 > /dev/null

php yii migrate --interactive=0
YII_ENV=test php yii migrate --interactive=0 > /dev/null

users=$(php -r '$pdo = new PDO(getenv("DB_DSN"), getenv("DB_USER"), getenv("DB_PASSWORD")); echo $pdo->query("SELECT COUNT(*) FROM `user`")->fetchColumn();')

if [ "$users" = 0 ]; then
    YII_USER_PASSWORD="$ADMIN_PASSWORD" php yii user/create --name=admin --email="$ADMIN_EMAIL" --interactive=0
fi

php yii setup --interactive=0

echo "Serving http://localhost:${PORT:-8080}, sign in at http://localhost:${PORT:-8080}/admin"
exec php -S 0.0.0.0:8080 -t web
