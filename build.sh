#!/usr/bin/env bash
# Terminar el proceso inmediatamente si algún comando falla
set -e

echo "Instalando dependencias de PHP..."
composer install --no-dev --optimize-autoloader --prefer-dist

echo "Instalando dependencias de Node.js..."
npm install

echo "Compilando recursos estáticos (Vite)..."
npm run build

echo "Limpiando caches anteriores..."
php artisan optimize:clear

echo "Cacheando configuración y rutas para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Creando enlace simbólico para almacenamiento..."
php artisan storage:link || true

echo "Ejecutando migraciones (Forzado para producción)..."
php artisan migrate --force

echo "Build completado con éxito."
