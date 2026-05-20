# -------------------------------------------------------
# Stage 1: Build stage — PHP + Node.js together
# The wayfinder Vite plugin calls `php artisan wayfinder:generate`
# during `npm run build`, so PHP must be present in this stage.
# -------------------------------------------------------
FROM php:8.2-cli-alpine AS builder

# Install Node.js, npm, and libraries needed by PHP extensions
RUN apk add --no-cache \
    nodejs \
    npm \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip

# Install PHP extensions required by Laravel (needed for artisan bootstrap)
RUN docker-php-ext-install pdo pdo_pgsql pgsql zip gd mbstring

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install Composer dependencies
COPY composer*.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies
COPY package.json ./
RUN npm install --no-audit

# Copy full source and provide a minimal .env so artisan can bootstrap
COPY . .
RUN echo "APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=" > .env \
    && echo "APP_ENV=production" >> .env \
    && echo "DB_CONNECTION=sqlite" >> .env \
    && echo "DB_DATABASE=/tmp/build.sqlite" >> .env

# Build frontend assets (wayfinder plugin will call php artisan internally)
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

WORKDIR /var/www/html

# Copy all application source files
COPY . .

# Copy pre-built frontend assets and vendor from builder stage
COPY --from=builder /app/public/build ./public/build
COPY --from=builder /app/vendor ./vendor

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
