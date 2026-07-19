#!/bin/sh
# =============================================================================
# Production Start Script for Kovai-Trans (Render.com)
# =============================================================================
set -e

APP_DIR="/var/www/html"
cd "$APP_DIR"

# ---------------------------------------------------------------------------
# 1. Set the port Nginx listens on (Render sets $PORT, default 80)
# ---------------------------------------------------------------------------
LISTEN_PORT="${PORT:-80}"
echo "[start.sh] Configuring Nginx to listen on port $LISTEN_PORT..."
sed -i "s/listen 80;/listen ${LISTEN_PORT};/" /etc/nginx/http.d/default.conf

# ---------------------------------------------------------------------------
# 1. Clear stale bootstrap caches (file-only, no DB contact)
# ---------------------------------------------------------------------------
echo "[start.sh] Clearing bootstrap caches..."
rm -f "$APP_DIR/bootstrap/cache/config.php"
rm -f "$APP_DIR/bootstrap/cache/routes-v7.php"
rm -f "$APP_DIR/bootstrap/cache/services.php"
rm -f "$APP_DIR/bootstrap/cache/packages.php"
rm -f "$APP_DIR/bootstrap/cache/events.php"

# ---------------------------------------------------------------------------
# 2. Run database migrations safely
# ---------------------------------------------------------------------------
echo "[start.sh] Running migrations..."
php artisan migrate:safe
echo "[start.sh] Migrations done."

# ---------------------------------------------------------------------------
# 3. Cache config and routes (NOT views - view:cache causes issues in prod boot)
# ---------------------------------------------------------------------------
echo "[start.sh] Caching config and routes..."
php artisan config:cache
php artisan route:cache
echo "[start.sh] Caching done."

# ---------------------------------------------------------------------------
# 4. Create storage symlink
# ---------------------------------------------------------------------------
echo "[start.sh] Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

# ---------------------------------------------------------------------------
# 5. Fix permissions
# ---------------------------------------------------------------------------
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true

# ---------------------------------------------------------------------------
# 6. Start supervisord (nginx + php-fpm + queue + scheduler)
# ---------------------------------------------------------------------------
echo "[start.sh] All done. Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
