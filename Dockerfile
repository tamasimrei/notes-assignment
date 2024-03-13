FROM trafex/php-nginx:3.4.0

USER root
RUN apk add bash sudo \
    php82-iconv php82-simplexml php82-posix \
    php82-pdo php82-tokenizer php82-pdo_mysql \
    php82-xmlwriter \
    && sed -i "s/^;\(fastcgi\.logging\(\s*\)\?=\).*/\1 0/" /etc/php82/php.ini

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy default nginx config file to set file root
COPY docker/nginx-default.conf /etc/nginx/conf.d/default.conf

USER nobody
