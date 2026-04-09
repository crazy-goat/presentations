# Stage 1: install PHP dependencies and build PHAR
FROM php:8.3-cli-alpine AS builder

RUN apk add --no-cache curl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY server/composer.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --quiet

COPY server/server.php server/build-phar.php ./

RUN mkdir /build && \
    php -d phar.readonly=0 build-phar.php

# Stage 2: download micro.sfx (static PHP binary stub)
FROM alpine:3.21 AS micro

ARG MICRO_URL=https://github.com/crazywhalecc/static-php-cli/releases/download/2.4.0/php-8.3-micro-linux-x86_64-normal.tar.gz

RUN apk add --no-cache wget && \
    wget -q -O /tmp/micro.tar.gz "${MICRO_URL}" && \
    tar -xzf /tmp/micro.tar.gz -C /tmp && \
    chmod +x /tmp/micro.sfx

# Stage 3: combine micro.sfx + app.phar into single binary
FROM alpine:3.21 AS combiner

COPY --from=micro /tmp/micro.sfx /micro.sfx
COPY --from=builder /build/app.phar /app.phar

RUN cat /micro.sfx /app.phar > /presentation && chmod +x /presentation

# Stage 4: final scratch image
FROM scratch

COPY --from=combiner /presentation /presentation
COPY slides/ /slides/

EXPOSE 8080
ENTRYPOINT ["/presentation"]
