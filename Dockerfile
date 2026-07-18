# =============================================================================
# Kovai-Trans Production Dockerfile
# Multi-stage build: Vite assets + Composer deps + Baileys Node deps + PHP image
# =============================================================================

# ---------------------------------------------------------------------------
# Stage 1: Build frontend assets (Vite)
# ---------------------------------------------------------------------------
FROM node:20-alpine AS frontend-build

WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci --ignore-scripts
COPY vite.config.js ./
COPY resources/ ./resources/
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2: PHP dependencies (Composer)
# ---------------------------------------------------------------------------
FROM composer:2 AS composer-build

WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# ---------------------------------------------------------------------------
# Stage 3: WhatsApp Baileys Node.js dependencies
# ---------------------------------------------------------------------------
FROM node:20-alpine AS baileys-build

WORKDIR /app
COPY node-services/whatsapp-baileys/package.json node-services/whatsapp-baileys/package-lock.json* ./
RUN npm ci --omit=dev

# ---------------------------------------------------------------------------
# Stage 4: Final production image
# ---------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS production

# Install system dependencies + Node.js
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
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

# Install Redis PHP extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# PHP production configuration
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
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

# Copy PHP vendor dependencies
COPY --from=composer-build /app/vendor /var/www/html/vendor

# Copy application source
COPY . .

# Explicitly copy .env for production
COPY .env /var/www/html/.env

# Copy built frontend assets
COPY --from=frontend-build /app/public/build /var/www/html/public/build

# Copy Baileys node_modules (built in stage 3)
COPY --from=baileys-build /app/node_modules /var/www/html/node-services/whatsapp-baileys/node_modules

# Ensure required Laravel storage directories exist and are writable
RUN mkdir -p /var/www/html/storage/framework/views \
             /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/testing \
             /var/www/html/storage/logs \
             /var/www/html/storage/app/public \
             /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
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

# Expose port 80 (Nginx)
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=90s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Start via bootstrap script
CMD ["/start.sh"]
