FROM alpine:3.24

# The official mariadb image has no Alpine variant, so this uses Alpine's own package.
RUN apk add --no-cache mariadb mariadb-client \
    && mkdir -p /run/mysqld \
    && chown mysql:mysql /run/mysqld /var/lib/mysql

# Alpine's default config disables networking; other containers connect over TCP
RUN printf '[mariadbd]\nskip-networking=0\nbind-address=0.0.0.0\nskip-name-resolve\n' \
    > /etc/my.cnf.d/zz-docker.cnf

COPY mariadb-entrypoint.sh /usr/local/bin/mariadb-entrypoint.sh

USER mysql
VOLUME /var/lib/mysql
EXPOSE 3306

ENTRYPOINT ["mariadb-entrypoint.sh"]
