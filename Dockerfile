FROM php:7.4-apache

RUN apt-get update && apt-get upgrade -y && apt-get install -y git

RUN a2enmod rewrite
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
RUN echo "Listen 8080" >> /etc/apache2/ports.conf
RUN service apache2 restart

WORKDIR /var/www
COPY  ./ /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN COMPOSER_MEMORY_LIMIT=-1 composer install
RUN chown -R www-data:www-data /var/www/
EXPOSE 8080