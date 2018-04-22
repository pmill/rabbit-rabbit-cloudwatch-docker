FROM php:7.1.0-fpm-alpine
MAINTAINER Peter Millard <dev.pmill@gmail.com>

ADD app.tar.gz /

ENTRYPOINT ["php", "/app/index.php", "/app/config.json"]