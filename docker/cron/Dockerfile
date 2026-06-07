FROM php:8.4.21-fpm-alpine

RUN apk --no-cache add dcron bash

COPY docker/cron/crontab /etc/crontabs/root

CMD ["crond", "-f", "-l", "2"]
