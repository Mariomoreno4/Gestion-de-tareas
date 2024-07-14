# Usar una imagen base de PHP con Apache
FROM php:8.0-apache

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar los archivos del proyecto al directorio raíz del servidor web
COPY . /var/www/html/

# Dar permisos adecuados a los archivos
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto 80
EXPOSE 80
