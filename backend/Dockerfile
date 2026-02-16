# backend/Dockerfile
FROM php:8.2-fpm

# 1. Instalar dependencias (incluyendo SQLite)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libsqlite3-dev

# 2. Instalar extensiones
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip

# 3. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 4. Copiar archivos clave
COPY composer.json composer.lock ./
ENV COMPOSER_PROCESS_TIMEOUT=2000

# 5. Instalar dependencias
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# 6. Copiar código
COPY . .

# 7. Autoload
RUN composer dump-autoload --optimize

# 8. Permisos (Vital para SQLite)
# Le damos permisos a la carpeta database para que pueda escribir el archivo
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database

# 9. Puerto
EXPOSE 80

# --- EL CAMBIO DEFINITIVO ---
# En una sola línea:
# 1. Crea el archivo vacio (si no existe)
# 2. Le da permisos para que nadie se queje
# 3. Crea las tablas (migrate)
# 4. Arranca el servidor
CMD sh -c "touch /var/www/database/database.sqlite && chmod 777 /var/www/database/database.sqlite && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=80"