# Use official PHP image with Apache
FROM php:8.2-apache

# Install PDO and PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install certificates for https
RUN a2enmod ssl && a2enmod rewrite
RUN mkdir -p /etc/apache2/ssl
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Expose ports for web access
EXPOSE 80   
EXPOSE 443
