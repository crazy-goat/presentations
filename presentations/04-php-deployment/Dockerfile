FROM php:8.3-cli-alpine AS builder

ARG PHP_MICRO_VERSION=8.3.20
ARG TARGETARCH=amd64

RUN apk add --no-cache curl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

RUN ARCH=$([ "$TARGETARCH" = "arm64" ] && echo "aarch64" || echo "x86_64") && \
    curl -fsSL "https://dl.static-php.dev/static-php-cli/minimal/php-${PHP_MICRO_VERSION}-micro-linux-${ARCH}.tar.gz" \
    -o micro.tar.gz && \
    tar -xzf micro.tar.gz && \
    mkdir -p build && \
    mv micro.sfx build/php8.3.micro.sfx && \
    rm micro.tar.gz

COPY webman/composer.json webman/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --quiet

COPY webman/ ./

RUN php -d phar.readonly=0 build-phar.php

FROM scratch

COPY --from=builder /app/build/webman.bin /webman

EXPOSE 8080
ENTRYPOINT ["/webman", "start"]
