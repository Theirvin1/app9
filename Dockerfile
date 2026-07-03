FROM php:8.3-apache

# Instalar dependencias para PostgreSQL y la extensión nativa PDO_PGSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activar mod_rewrite de Apache para soportar el enrutador
RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . /var/www/html/

# Configurar permisos para el usuario Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
