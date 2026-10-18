# PHP with the project's extensions, Composer and Node.js, for running the site on a machine without them. Made for
# development and trying the project out, not for production: it serves through PHP's built-in server. The newest
# PHP rather than the oldest the project supports, as nothing else on the machine depends on it.
FROM php:8.5-cli-trixie

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

# Trixie's Node.js is 20.19, the oldest one Vite supports. The migrations back the database up with `mysqldump`,
# which the compat package names MariaDB's.
RUN install-php-extensions exif imagick intl pdo_mysql zip \
    && apt-get update \
    && apt-get install --yes --no-install-recommends git mariadb-client-compat nodejs npm unzip \
    && rm -rf /var/lib/apt/lists/*

# The container runs as the owner of the project folder, who has no home directory or account in the image, and
# git refuses a repository that another user owns.
ENV HOME=/tmp COMPOSER_HOME=/tmp/composer
RUN git config --system --add safe.directory '*' \
    && printf 'memory_limit = 512M\nupload_max_filesize = 64M\npost_max_size = 64M\n' > /usr/local/etc/php/conf.d/website.ini

WORKDIR /app
EXPOSE 8080
