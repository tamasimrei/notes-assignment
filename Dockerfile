FROM trafex/php-nginx:latest

USER root
RUN apk add bash sudo \
    php81-iconv php81-simplexml php81-posix \
    php81-pdo php81-tokenizer php81-pdo_mysql \
    php81-xmlwriter \
    && sed -i "s/^;\(fastcgi\.logging\(\s*\)\?=\).*/\1 0/" /etc/php81/php.ini

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy default nginx config file to set file root
COPY docker/nginx-default.conf /etc/nginx/conf.d/default.conf

USER nobody
