# backend/Dockerfile
FROM php:8.2-fpm

# 1. Instalar dependencias del sistema y herramientas de compresión
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# 2. Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 4. Copiar archivos de definición
COPY composer.json composer.lock ./

# --- SOLUCIÓN DEL ERROR ---
# Aumentamos el tiempo de espera a 2000 segundos (33 minutos)
# para que no falle si tu internet o disco van lentos.
ENV COMPOSER_PROCESS_TIMEOUT=2000

# 5. Instalar dependencias
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# 6. Copiar el resto del código
COPY . .

# 7. Generar el autoloader final
RUN composer dump-autoload --optimize

# 8. Dar permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Puerto y comando para HTTP
EXPOSE 9000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=9000"]