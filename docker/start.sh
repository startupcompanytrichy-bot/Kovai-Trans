#!/bin/bash
# =============================================================================
# Production Start Script for Kovai-Trans
# =============================================================================

# Run migrations
php artisan migrate --force 2>/dev/null

# Set WhatsApp Baileys URL if not configured
php artisan tinker --execute="
if (empty(\App\Models\Setting::getValue('whatsapp_baileys_url'))) {
    \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_baileys_url'], ['value' => 'http://localhost:3001', 'group' => 'whatsapp', 'label' => 'Baileys Service URL']);
}
" 2>/dev/null

# Start WhatsApp Baileys service in background
cd /var/www/html/node-services/whatsapp-baileys
node server.js &
BAILEYS_PID=$!

# Go back to app root
cd /var/www/html

# Start PHP built-in server
php artisan serve --host=0.0.0.0 --port=${PORT:-10000} &
PHP_PID=$!

# Wait for both processes
wait $BAILEYS_PID $PHP_PID
