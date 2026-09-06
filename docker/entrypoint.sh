#!/usr/bin/env bash
set -euo pipefail

APP_DIR=/app
cd "$APP_DIR"

log() { echo "[entrypoint] $*"; }

# Everything that touches the application runs as www-data. Only the ownership
# repair below needs root, because a freshly attached volume arrives owned by
# root and would otherwise be unwritable by the server process.
as_app() { su-exec www-data "$@"; }

# ---------------------------------------------------------------------------
# 1. Rebuild the storage tree.
#    A Railway volume mounted at /app/storage starts EMPTY and hides whatever
#    the image baked in, so these directories must be recreated on every boot
#    or Laravel fails on its first cache/session/log write.
# ---------------------------------------------------------------------------
mkdir -p \
    storage/app/public \
    storage/app/media \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# ---------------------------------------------------------------------------
# 2. Fail fast on the variables that silently break everything
# ---------------------------------------------------------------------------
if [ -z "${APP_KEY:-}" ]; then
    log "FATAL: APP_KEY is not set. Generate one with 'php artisan key:generate --show'"
    log "       and add it as a Railway variable. Never rotate it: it decrypts stored data."
    exit 1
fi

if [ -z "${APP_URL:-}" ]; then
    log "WARNING: APP_URL is not set. Media/storage URLs and PDF asset paths will be wrong."
fi

if [ -z "${DB_DATABASE:-}" ]; then
    log "FATAL: DB_DATABASE is not set. Reference the MySQL service, e.g."
    log "       DB_DATABASE=\${{MySQL.MYSQLDATABASE}}"
    exit 1
fi

# ---------------------------------------------------------------------------
# 3. Wait for the database.
#    Provider boot itself tolerates a missing database, so this is no longer
#    what keeps the container alive. It still matters for the steps below:
#    migrations must not start against a database that is accepting no
#    connections, and a container that goes healthy before its data layer is
#    ready would serve errors to real traffic.
#
#    The DSN names the database on purpose: Laravel's connector issues a
#    USE <database>, so a probe that only opened the server port could go
#    green while the app still died on a missing schema or absent grant.
# ---------------------------------------------------------------------------
DB_WAIT_TIMEOUT="${DB_WAIT_TIMEOUT:-60}"
waited=0
until php -r '
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s",
        getenv("DB_HOST") ?: "127.0.0.1",
        getenv("DB_PORT") ?: "3306",
        getenv("DB_DATABASE")
    );
    try { new PDO($dsn, getenv("DB_USERNAME"), getenv("DB_PASSWORD")); exit(0); }
    catch (Throwable $e) { exit(1); }
' 2>/dev/null; do
    if [ "$waited" -ge "$DB_WAIT_TIMEOUT" ]; then
        log "FATAL: database ${DB_DATABASE} unreachable at ${DB_HOST:-unset}:${DB_PORT:-unset} after ${DB_WAIT_TIMEOUT}s"
        exit 1
    fi
    log "waiting for database... (${waited}s/${DB_WAIT_TIMEOUT}s)"
    sleep 3
    waited=$((waited + 3))
done
log "database ${DB_DATABASE} reachable"

# public/storage and public/media are .gitignored symlinks, so they are absent
# from the image and must be recreated.
as_app php artisan storage:link --force --quiet || log "storage:link failed (continuing)"

# ---------------------------------------------------------------------------
# 4. Migrations — enable on the web service only, never on the worker/scheduler
# ---------------------------------------------------------------------------
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    log "running migrations"
    as_app php artisan migrate --force --no-interaction
fi

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    log "running seeders"
    as_app php artisan db:seed --force --no-interaction
fi

# ---------------------------------------------------------------------------
# 5. Warm the framework caches. Safe because application code reads config()
#    rather than env()/Env::get(), so every value is baked into the cached
#    config tree here and nothing depends on the environment after this point.
#
#    Run as www-data so the cache files are owned by the process that reads
#    them, not by root.
# ---------------------------------------------------------------------------
as_app php artisan config:cache
as_app php artisan route:cache
as_app php artisan view:cache
as_app php artisan event:cache || true

log "boot complete, handing off to: $*"

# FrankenPHP reads $PORT from the environment via the Caddyfile, so no template
# rendering is needed here. Drop root for the server process itself.
exec su-exec www-data "$@"
