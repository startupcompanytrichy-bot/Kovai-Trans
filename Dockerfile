# =============================================================================
# Kovai-Trans Production Dockerfile
# Multi-stage build for Laravel + Node.js WhatsApp Baileys Service
# =============================================================================

# ---------------------------------------------------------------------------
# Stage 1: Build frontend assets (Node.js)
# ---------------------------------------------------------------------------
FROM node:20-alpine AS frontend-build

WORKDIR /app

# Copy package files first for better layer caching
COPY package.json package-lock.json* ./

# Install npm dependencies
RUN npm ci --ignore-scripts

# Copy Vite config and source files
COPY vite.config.js ./
COPY resources/ ./resources/

# Build production assets
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2: PHP dependencies (Composer)
# ---------------------------------------------------------------------------
FROM composer:2 AS composer-build

WORKDIR /app

# Copy composer files first for better layer caching
COPY composer.json composer.lock* ./

# Install dependencies (no dev for production)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# ---------------------------------------------------------------------------
# Stage 3: Final production image
# ---------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    libpq-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        zip \
        bcmath \
        mbstring \
        xml \
        intl \
        opcache \
        gd

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# PHP production configuration
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Custom PHP settings for production
RUN echo "upload_max_filesize = 64M" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "post_max_size = 64M" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "memory_limit = 512M" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "max_execution_time = 600" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "opcache.enable=1" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "opcache.memory_consumption=256" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "opcache.interned_strings_buffer=16" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "opcache.max_accelerated_files=20000" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "opcache.validate_timestamps=0" >> "$PHP_INI_DIR/conf.d/custom.ini"

WORKDIR /var/www/html

# Copy application files from composer stage
COPY --from=composer-build /app/vendor /var/www/html/vendor
COPY . .

# Explicitly copy .env for production
COPY .env /var/www/html/.env

# Copy built frontend assets
COPY --from=frontend-build /app/public/build /var/www/html/public/build

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Create required runtime directories
RUN mkdir -p /var/log/supervisor /var/run

# Expose ports: 80 (Nginx), 9000 (PHP-FPM)
EXPOSE 80 9000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Start via bootstrap script (writes .env, runs migrations, starts supervisord)
CMD ["/start.sh"]
