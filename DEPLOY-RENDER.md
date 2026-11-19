# Despliegue de UniEmpleo en Render

La app móvil no debe conectarse directamente a Laravel. La arquitectura queda:

```text
Expo → Next.js API Gateway → Laravel API → PostgreSQL
                   └────→ MySQL independiente para auditoría Prisma
```

Los servicios Laravel y Next.js se despliegan por separado. Si se publican en
un monorepo, ambos directorios deben estar en el repositorio conectado a Render;
si están en repositorios distintos, crea cada servicio desde su propio
repositorio y ajusta su **Root Directory** a `.`. El directorio Laravel incluye
un `Dockerfile` para Render; el gateway se ejecuta como servicio Node. Render
solo puede compilar código que esté enviado al repositorio remoto.

No subas archivos `.env`, credenciales ni datos de prueba al repositorio. El
`.dockerignore` excluye archivos sensibles de la imagen, pero no sustituye al
`.gitignore` ni protege archivos que ya se hayan enviado a Git.

## 1. Bases de datos

1. Crea una base de datos PostgreSQL en Render para Laravel y conserva su URL
   **interna** para conectarla desde el servicio Docker.
2. Provisiona una base MySQL independiente para el gateway Prisma. El esquema
   Prisma usa el proveedor `mysql` y contiene tablas con nombres también usados
   por Laravel; no compartas con Prisma la misma base de datos de Laravel.
3. Usa las URLs privadas/internas del proveedor cuando los servicios estén en
   la misma región/red. No pongas URLs, contraseñas ni claves dentro del
   repositorio.

Render ofrece PostgreSQL administrado, pero el esquema actual de Prisma requiere
MySQL. Para usar PostgreSQL también en el gateway habría que migrar y probar el
esquema Prisma; no basta con cambiar la URL.

## 2. Servicio Laravel

En Render crea **New → Web Service** desde el repositorio y configura:

- **Root Directory:** `Bolsa-empleo-unipaz-main`
- **Runtime:** Docker
- **Dockerfile Path:** `./Dockerfile`
- **Docker Context Directory:** `.`
- **Health Check Path:** `/api/v1/jobs`
- **Port:** `10000` (el Dockerfile configura Apache para escuchar este puerto)
- **Persistent Disk para CV y logos:** montar en
  `/var/www/html/storage/app/public`, o usar un almacenamiento S3-compatible.

Variables de entorno mínimas:

| Variable | Valor |
| --- | --- |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Clave estable generada una vez con `php artisan key:generate --show` |
| `APP_URL` | URL HTTPS pública del servicio Laravel |
| `DB_CONNECTION` | `pgsql` |
| `DATABASE_URL` | URL interna de PostgreSQL de Laravel |
| `FILESYSTEM_DISK` | `public` |
| `LOG_LEVEL` | `warning` o `error` |

El contenedor ejecuta `php artisan migrate --force` al iniciar y publica
`storage:link`. `APP_KEY` debe existir antes del primer arranque y mantenerse
igual entre despliegues. No ejecutes `php artisan db:seed` en producción: los
seeders incluyen datos de demostración.

El sistema guarda CV y logos en el disco público de Laravel. El filesystem de
Render es efímero por defecto; sin disco persistente o almacenamiento externo,
esos archivos se perderán al reiniciar o desplegar. Un disco persistente de
Render requiere un servicio de pago. Si se usa almacenamiento S3-compatible,
configura las variables `AWS_*` y el disco `s3` en el servicio Laravel.

Opcionales según las funciones habilitadas:

- Google: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` y
  `GOOGLE_REDIRECT_URI`; el client ID debe corresponder al que usa la app móvil.
- Correo: configura un proveedor SMTP con las variables `MAIL_*`; `MAIL_MAILER=log`
  no envía correos.

## 3. Servicio Next.js API Gateway

Crea otro **Web Service** desde el mismo repositorio:

- **Root Directory:** `bolsa-empleo-MOVIL-main/UniEmpleo/src/Api`
- **Runtime:** Node
- **Build Command:** `npm ci --include=dev && npx prisma generate && npm run build`
- **Start Command:** `npm run start`
- **Health Check Path:** `/api/v1`

Variables:

| Variable | Valor |
| --- | --- |
| `LARAVEL_API_URL` | URL HTTPS base de Laravel, sin `/api/v1` |
| `DATABASE_URL` | URL MySQL de Prisma, separada de la base Laravel |
| `JWT_SECRET` | Secreto aleatorio de al menos 32 caracteres |
| `CORS_ALLOWED_ORIGINS` | Orígenes web permitidos, separados por comas |

El gateway valida la conexión con Laravel desde su ruta de salud. La app móvil
recibe el dominio público del gateway; no uses el dominio Laravel como URL de
la app.

Como el repositorio todavía no contiene migraciones Prisma, inicializa una sola
vez el esquema en la base MySQL independiente, con `DATABASE_URL` apuntando a
esa base vacía y desde el directorio del gateway:

```powershell
npx prisma db push
```

No ejecutes este comando contra la base de Laravel. El build solo genera el
cliente Prisma; `db push` crea las tablas que usa el registro de auditoría.

## 4. Configurar y compilar Expo

Antes de crear una build de la app, configura en el entorno de compilación Expo:

```dotenv
EXPO_PUBLIC_API_URL=https://<gateway-render>.onrender.com/api/v1
```

El valor se incluye en el bundle de la app. Después de cambiarlo, vuelve a
generar la build móvil; reiniciar el servidor de desarrollo no actualiza una
build ya instalada.

`CORS_ALLOWED_ORIGINS` es relevante para Expo Web. La app nativa usa HTTPS y el
token Bearer; no necesita que CORS habilite `*`.

## 5. Comprobaciones después del despliegue

1. `GET https://<laravel-render>/api/v1/jobs` debe responder HTTP 200.
2. `GET https://<gateway-render>/api/v1` debe indicar que Laravel está
   conectado; un estado `degraded` significa que el gateway no alcanza Laravel
   o su endpoint público de vacantes.
3. Prueba login, perfil, listado/detalle de vacantes y una postulación con una
   cuenta de prueba. Verifica también una carga de CV y que el archivo siga
   accesible después de reiniciar el servicio.
4. No declares la integración desplegada hasta probar estos pasos contra las
   URLs reales de Render.

## Fuentes oficiales

- [Render: desplegar Laravel con Docker](https://render.com/docs/deploy-php-laravel-docker)
- [Render: servicios web y binding de puerto](https://render.com/docs/web-services#port-binding)
- [Render: discos persistentes](https://render.com/docs/disks)
- [Render: PostgreSQL](https://render.com/docs/postgresql)
- [Laravel 12: despliegue y raíz pública](https://laravel.com/docs/12.x/deployment#nginx)
- [Laravel 12: migraciones de base de datos](https://laravel.com/docs/12.x/migrations#updating-tables)
