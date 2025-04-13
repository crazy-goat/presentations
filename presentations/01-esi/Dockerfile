FROM alpine:3.21 AS base
RUN apk add --no-cache php82-fpm nginx traefik supervisor

COPY config/nginx/default.conf /etc/nginx/http.d/default.conf
COPY config/traefik /etc/traefik
COPY config/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY demo /var/www/demo
COPY slides /var/www/slides

RUN sed -i 's/^pm = dynamic/pm = static/' /etc/php82/php-fpm.d/www.conf \
    && sed -i 's/^pm.max_children = [0-9]\+/pm.max_children = 16/' /etc/php82/php-fpm.d/www.conf

WORKDIR /var/www/slides

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

FROM base AS dev
COPY config/supervisor/conf.d/supervisord-dev.conf /etc/supervisor/conf.d/supervisord.conf
ENV CI=true

WORKDIR /var/www/slides
RUN apk add --no-cache npm
ENV PUPPETEER_SKIP_DOWNLOAD=true
RUN npm i

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]