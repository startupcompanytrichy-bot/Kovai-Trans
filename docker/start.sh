#!/bin/bash
# =============================================================================
# Production Start Script for Kovai-Trans
# Writes .env from Render environment variables, then starts all services
# =============================================================================

# Write .env from environment variables
cat > /var/www/html/.env <<EOF
APP_NAME="${APP_NAME:-Kovai Trans}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-base64:j6BiCL6/o4a3MPU4Lv7CBUZvrKJkaiuW4UYy9Km2wVo=}
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
DB_HOST=${DB_HOST:-dpg-d901jhbeo5us73boin5g-a.oregon-postgres.render.com}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-kovai_transport}
DB_USERNAME=${DB_USERNAME:-kovai_transport_user}
DB_PASSWORD=${DB_PASSWORD:-Hh6TAg5LuiS5MNIV5fDEoUKpeNpCJHZu}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
CACHE_STORE=${CACHE_STORE:-database}

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

JWT_SECRET=${JWT_SECRET:-a0UwWVJWOUk2R1FJTzhHMG9JM3JDZG95RzZidVdmOXFGTHZ0SzZDVkJoSXd1aWxnTWl5VEx0QjdmNkdSTnBWbw==}
JWT_TTL=120
JWT_REMEMBER_TTL=43200
JWT_COOKIE=auth_token

MAIL_MAILER=log
EOF

# Run migrations
php artisan config:clear 2>/dev/null
php artisan migrate --force 2>/dev/null

# Start WhatsApp Baileys service in background
cd /var/www/html/node-services/whatsapp-baileys
node server.js &
BAILEYS_PID=$!

# Go back to app root
cd /var/www/html

# Start PHP built-in server
php artisan serve --host=0.0.0.0 --port=${PORT:-10000} &
PHP_PID=$!

wait $BAILEYS_PID $PHP_PID
