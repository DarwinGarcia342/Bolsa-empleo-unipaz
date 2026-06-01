#!/bin/bash
set -e

echo "=== Bolsa de Empleo UNIPAZ — Iniciando deploy ==="

# Si la aplicación usa SQLite, crear la base de datos y su carpeta si no existen.
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    SQLITE_DB_PATH="${DB_DATABASE:-database/database.sqlite}"
    mkdir -p "$(dirname "$SQLITE_DB_PATH")"
    touch "$SQLITE_DB_PATH"
fi

# Compilar assets frontend si el proyecto incluye package.json
if [ -f package.json ]; then
    echo "=== Instalando dependencias de Node y compilando assets ==="
    npm install
    npm run build
fi

# Limpiar cachés previos (evita errores con config cacheada vieja)
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones
php artisan migrate --force

# Crear enlace de almacenamiento público
php artisan storage:link || true

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar seeder solo si la tabla users está vacía
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "Sembrando datos iniciales..."
    php artisan db:seed --force
fi

echo "=== ¡Listo! Iniciando servidor ==="

# Iniciar servidor PHP en el puerto asignado por Railway
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
