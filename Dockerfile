FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

COPY . /var/www/html

RUN mkdir -p /var/www/html/backups \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
