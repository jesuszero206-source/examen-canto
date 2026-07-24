#!/usr/bin/env bash
# start.sh
# Terminar si hay algún error
set -e

# Asegurar configuración y optimización
echo "Ejecutando cache de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones forzadas
echo "Ejecutando migraciones..."
php artisan migrate --force

# Crear link simbólico del Storage si no existe
echo "Vinculando el storage..."
php artisan storage:link || true

echo "Levantando el servidor Apache..."
# Iniciar Apache en primer plano
exec apache2-foreground
