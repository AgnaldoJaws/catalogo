#!/bin/bash
set -e

log() {
  echo "[$(date '+%H:%M:%S')] $1"
}

cd /var/www/html

git config --global --add safe.directory /var/www/html

log "🔐 Corrigindo permissões..."
mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwX storage bootstrap/cache || true

log "⚙️ Criando arquivo .env..."
cp -n .env.example .env || log ".env já existe."

log "📦 Instalando dependências do Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader

log "🔑 Gerando APP_KEY..."
php artisan key:generate --force

log "🧹 Limpando caches..."
php artisan optimize:clear

log "📄 Executando migrations..."
php artisan migrate --force

if [ "$RUN_SEEDERS" = "true" ]; then
  log "🌱 Rodando seeders..."
  php artisan db:seed --force
fi

if [ -f "package.json" ]; then
  log "⚙️ Rodando build do front..."
  npm ci --no-audit --progress=false
  npm run build --if-present
fi

log "🚀 Iniciando Laravel Octane (Swoole)..."

if [ "$APP_ENV" = "local" ]; then
  log "👀 Ambiente local detectado — habilitando modo watch..."
  exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8005 --watch
else
  exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8005
fi
