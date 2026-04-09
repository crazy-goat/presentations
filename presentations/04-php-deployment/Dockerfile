FROM php:8.3-cli-alpine AS builder

ARG PHP_MICRO_VERSION=8.3.20
ARG TARGETARCH=amd64

RUN apk add --no-cache curl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Download micro.sfx (minimal build — no OS, no shell, just PHP)
RUN ARCH=$([ "$TARGETARCH" = "arm64" ] && echo "aarch64" || echo "x86_64") && \
    curl -fsSL "https://dl.static-php.dev/static-php-cli/minimal/php-${PHP_MICRO_VERSION}-micro-linux-${ARCH}.tar.gz" \
    -o micro.tar.gz && \
    tar -xzf micro.tar.gz && \
    mv micro.sfx build/php.micro.sfx && \
    rm micro.tar.gz

COPY server/composer.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --quiet

COPY server/ ./

RUN php -d phar.readonly=0 ./vendor/bin/webman build:bin

FROM scratch

COPY --from=builder /app/build/webman.bin /webman
COPY slides/ /slides/

EXPOSE 8080
ENTRYPOINT ["/webman", "start"]
