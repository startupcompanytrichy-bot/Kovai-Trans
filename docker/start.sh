#!/bin/sh
# =============================================================================
# Production Start Script for Kovai-Trans (Render.com)
# Bootstraps Laravel then starts services via Supervisord
# =============================================================================
set -e

APP_DIR="/var/www/html"
cd "$APP_DIR"

echo "[start.sh] Writing .env from environment variables..."
cat > "$APP_DIR/.env" <<EOF
APP_NAME="${APP_NAME:-Kovai Trans}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://kovai-trans.onrender.com}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
CACHE_STORE=${CACHE_STORE:-database}

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

JWT_SECRET=${JWT_SECRET}
JWT_TTL=120
JWT_REMEMBER_TTL=43200
JWT_COOKIE=auth_token

MAIL_MAILER=${MAIL_MAILER:-log}
MAIL_HOST=${MAIL_HOST:-127.0.0.1}
MAIL_PORT=${MAIL_PORT:-2525}
MAIL_USERNAME=${MAIL_USERNAME:-null}
MAIL_PASSWORD=${MAIL_PASSWORD:-null}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-noreply@kovai-trans.com}
MAIL_FROM_NAME="${APP_NAME:-Kovai Trans}"

WHATSAPP_BAILEYS_URL=${WHATSAPP_BAILEYS_URL:-http://localhost:3001}
WHATSAPP_DAILY_LIMIT=${WHATSAPP_DAILY_LIMIT:-100}

GOOGLE_MAPS_API_KEY=${GOOGLE_MAPS_API_KEY:-}
GEOAPIFY_API_KEY=${GEOAPIFY_API_KEY:-}
ORS_API_KEY=${ORS_API_KEY:-}

VITE_APP_NAME="${APP_NAME:-Kovai Trans}"
EOF

echo "[start.sh] Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "[start.sh] Running migrations..."
php artisan migrate --force

echo "[start.sh] Caching config, routes, and views for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[start.sh] Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

echo "[start.sh] Fixing storage permissions..."
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true

echo "[start.sh] Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
