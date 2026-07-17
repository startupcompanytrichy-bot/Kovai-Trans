# =============================================================================
# Kovai-Trans Production Dockerfile (Render.com)
# Runs: Laravel PHP + WhatsApp Baileys Node.js in single container
# =============================================================================

# ---------------------------------------------------------------------------
# Stage 1: Build frontend assets
# ---------------------------------------------------------------------------
FROM node:20-alpine AS frontend-build

WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci --ignore-scripts
COPY vite.config.js ./
COPY resources/ ./resources/
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2: PHP + Node.js runtime
# ---------------------------------------------------------------------------
FROM php:8.3-cli

# Install Node.js 20 for WhatsApp Baileys
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpq-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo pdo_pgsql zip bcmath mbstring xml intl opcache gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application files
COPY . .

# Copy built frontend assets from Node stage
COPY --from=frontend-build /app/public/build /var/www/html/public/build

# Install WhatsApp Baileys Node.js dependencies
RUN cd node-services/whatsapp-baileys && npm ci --omit=dev && cd /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/docker/start.sh

EXPOSE 10000 3001

# Start both services
CMD ["/var/www/html/docker/start.sh"]
