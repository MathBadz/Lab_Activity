# -------------------------------------------------------
# Stage 1: Build frontend assets with Node.js
# -------------------------------------------------------
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci --no-audit

COPY . .
RUN npm run build

# -------------------------------------------------------
# Stage 2: PHP-FPM + Nginx runtime
# -------------------------------------------------------
FROM php:8.2-fpm-alpine

# Install system packages: nginx, supervisor, postgres driver, and common extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip \
    curl

# Install PHP extensions required by Laravel
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    gd \
    mbstring \
    opcache

# Install Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy all application source files
COPY . .

# Overwrite public/build with assets compiled in the frontend stage
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies (production, no dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ensure storage and cache directories are writable by php-fpm (www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy nginx site config
COPY conf/nginx/nginx-site.conf /etc/nginx/http.d/default.conf

# Copy supervisor config
COPY conf/supervisord.conf /etc/supervisord.conf

# Copy and prepare startup scripts
COPY scripts/start.sh /start.sh
COPY scripts/00-laravel-deploy.sh /var/www/html/scripts/00-laravel-deploy.sh
RUN chmod +x /start.sh /var/www/html/scripts/00-laravel-deploy.sh

EXPOSE 80

CMD ["/start.sh"]
