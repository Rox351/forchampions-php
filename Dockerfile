FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite headers \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

RUN mkdir -p public/uploads public/assets/images/products public/assets/images/branding public/assets/images/tabelas \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80
