FROM trafex/php-nginx:3.11.1

USER root
RUN apk upgrade --no-cache \
    && apk add --no-cache bash sudo \
    php85-iconv php85-simplexml php85-posix \
    php85-pdo php85-tokenizer php85-pdo_mysql \
    php85-xmlwriter \
    && sed -i "s/^;\(fastcgi\.logging\(\s*\)\?=\).*/\1 0/" /etc/php85/php.ini \
    && echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED" > /etc/php85/conf.d/zz-no-deprecations.ini

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy default nginx config file to set file root
COPY docker/nginx-default.conf /etc/nginx/conf.d/default.conf

USER nobody
