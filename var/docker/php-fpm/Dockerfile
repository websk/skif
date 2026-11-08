FROM composer:2 AS composer

FROM golang:1.25-bookworm AS mhsendmail-builder

ARG MHSENDMAIL_VERSION=v0.2.0

WORKDIR /build

RUN set -eux; \
    git clone --depth 1 --branch "${MHSENDMAIL_VERSION}" https://github.com/mailhog/mhsendmail.git; \
    cd mhsendmail; \
    go mod init github.com/mailhog/mhsendmail; \
    go get github.com/ogier/pflag@v0.0.1; \
    CGO_ENABLED=0 go build -trimpath -ldflags="-s -w" -o /usr/local/bin/mhsendmail .

FROM php:8.5.12-fpm-bookworm

ARG INSTALL_XDEBUG=false
ARG INSTALL_ZIP_ARCHIVE=false
ARG INSTALL_MEMCACHED=false
ARG INSTALL_INTL=false
ARG INSTALL_PCNTL=false

ARG XDEBUG_VERSION=3.5.3
ARG MEMCACHED_VERSION=3.4.0
ARG MEMCACHED_SHA256=10b98a0fdc3ce9ab823db59e8935f068de87e374728871a1efacdadd8c7807d1
ARG PHP_BUILD_JOBS=2

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=mhsendmail-builder /usr/local/bin/mhsendmail /usr/local/bin/mhsendmail

RUN set -eux; \
    saved_apt_mark="$(apt-mark showmanual)"; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        ca-certificates \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libmemcached-dev \
        libpng-dev \
        libssl-dev \
        libwebp-dev \
        libzip-dev \
        unzip \
        zip \
        zlib1g-dev; \
    docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp; \
    docker-php-ext-install -j"${PHP_BUILD_JOBS}" \
        exif \
        gd \
        mysqli \
        pdo_mysql; \
    if [ "${INSTALL_ZIP_ARCHIVE}" = "true" ]; then \
        docker-php-ext-install -j"${PHP_BUILD_JOBS}" zip; \
    fi; \
    if [ "${INSTALL_INTL}" = "true" ]; then \
        docker-php-ext-install -j"${PHP_BUILD_JOBS}" intl; \
    fi; \
    if [ "${INSTALL_PCNTL}" = "true" ]; then \
        docker-php-ext-install -j"${PHP_BUILD_JOBS}" pcntl; \
    fi; \
    if [ "${INSTALL_MEMCACHED}" = "true" ]; then \
        cd /tmp; \
        curl -fsSLo php-memcached.tar.gz \
            "https://github.com/php-memcached-dev/php-memcached/archive/refs/tags/v${MEMCACHED_VERSION}.tar.gz"; \
        echo "${MEMCACHED_SHA256}  php-memcached.tar.gz" | sha256sum -c -; \
        tar -xzf php-memcached.tar.gz; \
        cd "php-memcached-${MEMCACHED_VERSION}"; \
        phpize; \
        PKG_CONFIG_PATH="/usr/lib/$(dpkg-architecture -qDEB_HOST_MULTIARCH)/pkgconfig" \
            ./configure \
                --with-php-config=/usr/local/bin/php-config \
                --with-libmemcached-dir=yes \
                --with-zlib-dir=/usr \
                --disable-memcached-igbinary \
                --disable-memcached-msgpack \
                --disable-memcached-json \
                --disable-memcached-protocol \
                --enable-memcached-sasl \
                --enable-memcached-session; \
        make -j"${PHP_BUILD_JOBS}"; \
        make install; \
        docker-php-ext-enable memcached; \
        cd /; \
        rm -rf /tmp/php-memcached*; \
    fi; \
    if [ "${INSTALL_XDEBUG}" = "true" ]; then \
        pecl install "xdebug-${XDEBUG_VERSION}"; \
        docker-php-ext-enable xdebug; \
    fi; \
    rm -rf /tmp/pear; \
    apt-mark auto '.*' > /dev/null; \
    apt-mark manual ${saved_apt_mark}; \
    apt-mark auto $PHPIZE_DEPS > /dev/null; \
    apt-mark manual ca-certificates unzip zip; \
    find "$(php -r 'echo ini_get("extension_dir");')" -type f -name '*.so' -exec ldd '{}' ';' \
        | awk '/=>/ { print $(NF - 1) }' \
        | sort -u \
        | xargs -r readlink -e \
        | xargs -r dpkg-query --search \
        | cut -d: -f1 \
        | sort -u \
        | xargs -r apt-mark manual; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*; \
    php -r 'exit(PHP_VERSION_ID === 80512 ? 0 : 1);'; \
    for extension in curl exif gd mbstring mysqli pdo_mysql; do \
        php -m | grep -Fqx "${extension}"; \
    done; \
    if [ "${INSTALL_ZIP_ARCHIVE}" = "true" ]; then php -m | grep -Fqx zip; fi; \
    if [ "${INSTALL_INTL}" = "true" ]; then php -m | grep -Fqx intl; fi; \
    if [ "${INSTALL_PCNTL}" = "true" ]; then php -m | grep -Fqx pcntl; fi; \
    if [ "${INSTALL_MEMCACHED}" = "true" ]; then php -m | grep -Fqx memcached; fi; \
    if [ "${INSTALL_XDEBUG}" = "true" ]; then php -m | grep -Fqx xdebug; fi; \
    composer --version; \
    test -x /usr/local/bin/mhsendmail

COPY ./php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY ./xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini
COPY ./phpfpm.pool.conf /usr/local/etc/php-fpm.d/zz-app.conf

RUN usermod -u 1000 www-data

WORKDIR /var/www

EXPOSE 9000

CMD ["php-fpm"]
