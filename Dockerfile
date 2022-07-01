FROM trafex/php-nginx:latest

USER root
RUN apk add bash sudo php81-iconv php81-simplexml php81-posix php81-pdo php81-tokenizer php81-pdo_mysql

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

USER nobody

# Run composer install to install the dependencies
RUN composer install --optimize-autoloader --no-interaction --no-progress
