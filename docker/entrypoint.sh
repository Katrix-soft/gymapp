#!/bin/sh
set -e

echo "[start] Iniciando contenedor Laravel Gym App..."

# ── 1. ENV ───────────────────────────────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    touch /var/www/html/.env
fi
printenv | grep -E "^(APP_|DB_|SESSION_|CACHE_|QUEUE_|MAIL_|REDIS_|LIVEWIRE_|MP_)" \
    | while IFS='=' read -r key value; do
        grep -q "^${key}=" /var/www/html/.env || echo "${key}=${value}" >> /var/www/html/.env
    done

# ── 2. APP KEY ────────────────────────────────────────────────────────────────
grep -q "APP_KEY=base64:" /var/www/html/.env || php artisan key:generate --force

# ── 3. PERMISOS ───────────────────────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── 4. CACHÉ DE LARAVEL ───────────────────────────────────────────────────────
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan view:cache
php artisan event:cache

# ── 4.5 ESPERAR A LA BASE DE DATOS ─────────────────────────────────────────────
echo "[start] Esperando a que la base de datos esté lista en ${DB_HOST:-127.0.0.1}:${DB_PORT:-3306}..."
max_attempts=15
attempt=1
while [ $attempt -le $max_attempts ]; do
    if php -r "
        \$host = getenv('DB_HOST') ?: '127.0.0.1';
        \$port = getenv('DB_PORT') ?: '3306';
        \$connection = @fsockopen(\$host, (int) \$port, \$errno, \$errstr, 2);
        if (is_resource(\$connection)) {
            fclose(\$connection);
            exit(0);
        }
        exit(1);
    "; then
        echo "[start] ¡Base de datos conectada con éxito!"
        break
    fi
    echo "[start] Base de datos no responde (intento $attempt/$max_attempts), esperando 2s..."
    sleep 2
    attempt=$((attempt + 1))
done

# ── 4.6 CREAR BASE DE DATOS SI NO EXISTE ───────────────────────────────────────
echo "[start] Asegurando existencia de la base de datos..."
php -r "
    \$host = getenv('DB_HOST') ?: '127.0.0.1';
    \$port = getenv('DB_PORT') ?: '3306';
    \$username = getenv('DB_USERNAME') ?: 'root';
    \$password = getenv('DB_PASSWORD') ?: '';
    \$database = getenv('DB_DATABASE') ?: 'forge';
    try {
        \$pdo = new PDO(\"mysql:host=\$host;port=\$port\", \$username, \$password);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        \$pdo->exec(\"CREATE DATABASE IF NOT EXISTS \\\`\$database\\\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\");
        echo \"[start] Base de datos '\$database' asegurada.\n\";
    } catch (PDOException \$e) {
        echo \"[start] Advertencia: \" . \$e->getMessage() . \"\n\";
    }
"

# ── 5. MIGRACIONES ────────────────────────────────────────────────────────────
echo "[start] Corriendo migraciones..."

LOCK_FILE="/var/www/html/storage/app/.migrate_lock"
CURRENT_HASH=$(find /var/www/html/database/migrations -name "*.php" | sort | xargs md5sum | md5sum | cut -d' ' -f1)

# Verificar si la tabla de migraciones existe en la base de datos
TABLE_EXISTS=$(php -r "
    \$host = getenv('DB_HOST') ?: '127.0.0.1';
    \$port = getenv('DB_PORT') ?: '3306';
    \$username = getenv('DB_USERNAME') ?: 'root';
    \$password = getenv('DB_PASSWORD') ?: '';
    \$database = getenv('DB_DATABASE') ?: 'forge';
    try {
        \$pdo = new PDO(\"mysql:host=\$host;port=\$port;dbname=\$database\", \$username, \$password);
        \$result = \$pdo->query(\"SHOW TABLES LIKE 'migrations'\");
        if (\$result && \$result->rowCount() > 0) {
            echo 'yes';
        }
    } catch (Exception \$e) {}
")

if [ "$TABLE_EXISTS" != "yes" ]; then
    echo "[start] La tabla de migraciones no existe. Borrando lock para forzar migración..."
    rm -f "$LOCK_FILE"
fi

if [ ! -f "$LOCK_FILE" ] || [ "$(cat $LOCK_FILE)" != "$CURRENT_HASH" ]; then
    echo "[start] Cambios detectados en migraciones o base de datos vacía, ejecutando..."
    php artisan migrate --force
    # Also run tenant migrations in the same DB (single shared database)
    echo "[start] Corriendo migraciones de tablas tenant en la misma DB..."
    php artisan migrate --path=database/migrations/tenant --force
    echo "$CURRENT_HASH" > "$LOCK_FILE"
    echo "[start] Lock actualizado: $CURRENT_HASH"
else
    echo "[start] Sin cambios en migraciones, saltando."
fi

# ── 5.1 SEEDER CENTRAL (solo SaaS Plans + CentralUser, SIN crear tenants) ─────
echo "[start] Verificando base central..."
CENTRAL_ADMIN_COUNT=$(php artisan tinker --execute="echo App\Models\CentralUser::count();" 2>/dev/null | tail -1)

if [ "$CENTRAL_ADMIN_COUNT" = "0" ] || [ -z "$CENTRAL_ADMIN_COUNT" ]; then
    echo "[start] No hay usuarios centrales, corriendo seeders centrales..."
    php artisan tinker --execute="
        \$seeder = new \Database\Seeders\DatabaseSeeder();
        \$seeder->run();
    " 2>/dev/null || true
    echo "[start] Seeders centrales completados."
else
    echo "[start] Ya existen usuarios centrales ($CENTRAL_ADMIN_COUNT), saltando seeder."
fi

# ── 5.2 ASEGURAR TENANT EN PRODUCCIÓN (via SQL directo, sin eventos) ───────────
echo "[start] Asegurando existencia del tenant de producción (gym1)..."
php -r "
    \$host = getenv('DB_HOST') ?: '127.0.0.1';
    \$port = getenv('DB_PORT') ?: '3306';
    \$username = getenv('DB_USERNAME') ?: 'root';
    \$password = getenv('DB_PASSWORD') ?: '';
    \$database = getenv('DB_DATABASE') ?: 'forge';
    try {
        \$pdo = new PDO(\"mysql:host=\$host;port=\$port;dbname=\$database\", \$username, \$password);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar si el tenant gym1 ya existe
        \$stmt = \$pdo->prepare('SELECT COUNT(*) FROM tenants WHERE id = ?');
        \$stmt->execute(['gym1']);
        \$exists = (int) \$stmt->fetchColumn();

        if (\$exists === 0) {
            echo \"[start] Creando tenant gym1 via SQL directo...\n\";
            \$now = date('Y-m-d H:i:s');
            \$data = json_encode(['name' => 'Gym Demo', 'owner_email' => 'admin@gymdemo.com']);

            \$stmt = \$pdo->prepare('INSERT INTO tenants (id, data, created_at, updated_at) VALUES (?, ?, ?, ?)');
            \$stmt->execute(['gym1', \$data, \$now, \$now]);

            \$stmt = \$pdo->prepare('INSERT INTO domains (domain, tenant_id, created_at, updated_at) VALUES (?, ?, ?, ?)');
            \$stmt->execute(['deploys-gymapp.juidi9.easypanel.host', 'gym1', \$now, \$now]);

            echo \"[start] Tenant gym1 + dominio creados exitosamente.\n\";
        } else {
            // Verificar que el dominio existe
            \$stmt = \$pdo->prepare(\"SELECT COUNT(*) FROM domains WHERE tenant_id = 'gym1' AND domain = ?\");
            \$stmt->execute(['deploys-gymapp.juidi9.easypanel.host']);
            \$domainExists = (int) \$stmt->fetchColumn();
            if (\$domainExists === 0) {
                \$now = date('Y-m-d H:i:s');
                \$stmt = \$pdo->prepare('INSERT INTO domains (domain, tenant_id, created_at, updated_at) VALUES (?, ?, ?, ?)');
                \$stmt->execute(['deploys-gymapp.juidi9.easypanel.host', 'gym1', \$now, \$now]);
                echo \"[start] Dominio de producción agregado al tenant gym1.\n\";
            } else {
                echo \"[start] Tenant gym1 y dominio ya existen.\n\";
            }
        }
    } catch (PDOException \$e) {
        echo \"[start] Error configurando tenant: \" . \$e->getMessage() . \"\n\";
    }
"

# ── 5.2.1 SEED TENANT DATA ────────────────────────────────────────────────────
echo "[start] Verificando seed del tenant gym1..."
php artisan tinker --execute="
    \$tenant = \App\Models\Tenant::find('gym1');
    if (\$tenant) {
        tenancy()->initialize(\$tenant);
        if (\App\Models\User::count() === 0) {
            echo 'Seeding tenant gym1...';
            \$seeder = new \Database\Seeders\DatabaseSeeder();
            \$seeder->run();
            echo 'Done.';
        } else {
            echo 'Tenant gym1 ya tiene datos.';
        }
        tenancy()->end();
    }
" 2>/dev/null || true

# ── 5.3 STORAGE LINK ─────────────────────────────────────────────────────────
echo "[start] Creando storage link..."
php artisan storage:link --force

# ── 5.4 LIVEWIRE ASSETS ──────────────────────────────────────────────────────
echo "[start] Publicando assets de Livewire..."
php artisan livewire:publish --assets || true

# ── 6. PHP-FPM con watchdog ───────────────────────────────────────────────────
echo "[start] Arrancando php-fpm..."
pkill -9 php-fpm 2>/dev/null || true
rm -f /var/run/php-fpm.pid
sleep 1

(
    while true; do
        php-fpm --nodaemonize || true
        echo "[watchdog] php-fpm cayó, reiniciando en 2s..."
        pkill -9 php-fpm 2>/dev/null || true
        rm -f /var/run/php-fpm.pid
        sleep 2
    done
) &
FPM_WATCHDOG_PID=$!

sleep 2

# ── 7. QUEUE WORKER ───────────────────────────────────────────────────────────
echo "[start] Arrancando queue worker..."
(
    while true; do
        php artisan queue:work \
            --sleep=3 \
            --tries=3 \
            --max-time=3600 \
            --memory=256 \
            --timeout=60 \
            --queue=default \
            || true
        echo "[watchdog] queue worker cayó, reiniciando en 3s..."
        sleep 3
    done
) &
QUEUE_PID=$!

# ── 7.5 SCHEDULE RUNNER ───────────────────────────────────────────────────────
echo "[start] Arrancando schedule runner..."
(
    while true; do
        php artisan schedule:work || true
        echo "[watchdog] schedule runner cayó, reiniciando en 5s..."
        sleep 5
    done
) &
SCHEDULE_PID=$!

# ── 8. NGINX ──────────────────────────────────────────────────────────────────
echo "[start] Arrancando nginx..."
trap "echo '[start] Apagando...'; kill $FPM_WATCHDOG_PID $QUEUE_PID $SCHEDULE_PID 2>/dev/null; nginx -s quit; exit 0" TERM INT

nginx -g "daemon off;"
