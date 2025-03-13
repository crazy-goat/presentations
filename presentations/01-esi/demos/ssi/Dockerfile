FROM alpine:3.21
RUN apk add --no-cache php82-fpm nginx supervisor
COPY config/default.conf /etc/nginx/http.d/default.conf
COPY www /var/www
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN sed -i 's/^pm = dynamic/pm = static/' /etc/php82/php-fpm.d/www.conf \
    && sed -i 's/^pm.max_children = [0-9]\+/pm.max_children = 8/' /etc/php82/php-fpm.d/www.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]