#!/bin/sh
# =============================================================================
# Production Start Script for Kovai-Trans (Render.com)
# =============================================================================

APP_DIR="/var/www/html"
cd "$APP_DIR"

# ---------------------------------------------------------------------------
# 1. Clear stale bootstrap caches (file-only, safe before DB is touched)
# ---------------------------------------------------------------------------
echo "[start.sh] Clearing bootstrap caches..."
rm -f "$APP_DIR/bootstrap/cache/config.php"
rm -f "$APP_DIR/bootstrap/cache/routes-v7.php"
rm -f "$APP_DIR/bootstrap/cache/services.php"
rm -f "$APP_DIR/bootstrap/cache/packages.php"
rm -f "$APP_DIR/bootstrap/cache/events.php"

# ---------------------------------------------------------------------------
# 2. Run database migrations
# ---------------------------------------------------------------------------
echo "[start.sh] Running migrations..."
php artisan migrate --force
echo "[start.sh] Migrations done."

# ---------------------------------------------------------------------------
# 3. Cache config, routes, views for production performance
# ---------------------------------------------------------------------------
echo "[start.sh] Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
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
echo "[start.sh] Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
