FROM alpine:3.21 AS base
RUN apk add --no-cache nginx wget \
    && wget https://github.com/ochinchina/supervisord/releases/download/v0.7.3/supervisord_0.7.3_Linux_64-bit.tar.gz \
    && tar -xvf supervisord_0.7.3_Linux_64-bit.tar.gz \
    && cp supervisord_0.7.3_Linux_64-bit/supervisord /usr/bin/supervisord \
    && rm -rf supervisord_0.7.3_Linux_64-bit supervisord_0.7.3_Linux_64-bit.tar.gz \
    && apk del wget

COPY config/nginx/default.conf /etc/nginx/http.d/default.conf
COPY config/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY slides /var/www/slides

WORKDIR /var/www/slides

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

FROM base AS dev
COPY config/supervisor/conf.d/supervisord-dev.conf /etc/supervisor/conf.d/supervisord.conf
ENV CI=true

WORKDIR /var/www/slides
RUN apk add --no-cache npm

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]