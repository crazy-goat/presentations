FROM php:8.3-cli-alpine AS builder

RUN apk add --no-cache curl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY server/composer.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --quiet

COPY server/ ./

RUN php -d phar.readonly=0 ./vendor/bin/webman build:bin

FROM scratch

COPY --from=builder /app/build/webman.bin /webman
COPY slides/ /slides/

EXPOSE 8080
ENTRYPOINT ["/webman", "start"]
