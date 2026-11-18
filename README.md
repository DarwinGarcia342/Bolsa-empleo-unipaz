# Bolsa de Empleo UNIPAZ

Documentación general del proyecto desarrollado en Laravel para gestionar la relación entre estudiantes, empresas y administración de la Universidad de Paz UNIPAZ.

<<<<<<< HEAD
## 1. Descripción general
=======
## Sobre este proyecto

Este repositorio contiene la aplicación web de la Bolsa de Empleo UNIPAZ, basada en Laravel 12. Está preparada para ejecutarse localmente con SQLite y desplegarse en Railway usando el script `start.sh`.

## Instrucciones rápidas

1. Copia `.env.example` a `.env`
2. Ejecuta `composer install`
3. Ejecuta `npm install`
4. Ejecuta `php artisan key:generate`
5. Ejecuta `php artisan migrate`
6. Ejecuta `npm run build`
7. Ejecuta `php artisan serve --host=0.0.0.0 --port=8000`

Para despliegue en Railway, consulta `DEPLOY-RAILWAY.md`.

## About Laravel
>>>>>>> 5e84259e209edb9ce657da3abc8036b3b9b768bb

Este sistema permite:

- Registrar empresas interesadas en publicar vacantes.
- Aprobar o rechazar empresas desde un panel administrativo.
- Gestionar vacantes laborales por parte de empresas aprobadas.
- Permitir a estudiantes buscar oportunidades, filtrar por área, modalidad y tipo de contrato.
- Aplicar a vacantes con CV y carta de presentación.
- Revisar y actualizar el estado de cada postulación.
- Enviar notificaciones internas y por correo a los actores del sistema.
- Implementar autenticación con correo y contraseña, además de inicio de sesión con Google para estudiantes.

El proyecto está construido con Laravel 12, PHP 8.2, Blade como motor de vistas y MySQL como motor de base de datos recomendado.

## 2. Objetivo del sistema

La aplicación tiene como finalidad centralizar la bolsa de empleo institucional, facilitando la conexión entre:

- estudiantes que buscan prácticas o empleo,
- empresas que requieren talento humano,
- y la universidad que supervisa y valida los procesos.

## 3. Roles del sistema

### Administrador

Funciones principales:

- Acceder al dashboard administrativo.
- Revisar empresas registradas.
- Aprobar o rechazar compañías.
- Gestionar usuarios.
- Activar o desactivar cuentas.
- Ver vacantes y postulaciones.
- Consultar reportes por áreas, empresas y estados de aplicación.

### Empresa

Funciones principales:

- Crear perfil de empresa.
- Publicar vacantes.
- Editar, pausar o cerrar vacantes.
- Revisar postulaciones recibidas.
- Actualizar el estado de una aplicación: pendiente, en revisión, entrevista, aceptado o rechazado.

### Estudiante

Funciones principales:

- Iniciar sesión con correo institucional o Google.
- Completar su perfil académico.
- Buscar vacantes por filtros.
- Ver detalles de las oportunidades.
- Aplicar a vacantes.
- Consultar el historial de sus postulaciones.

## 4. Tecnologías utilizadas

- PHP 8.2
- Laravel 12
- Composer
- MySQL / MariaDB
- Blade templates
- Vite para assets front-end
- Laravel Socialite para autenticación con Google
- Laravel Notifications para mensajes del sistema
- Bootstrap o estilos personalizados con Blade + CSS/JS

## 5. Estructura del proyecto

```text
bolsa-empleo-unipaz/
├── app/
│   ├── Console/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   ├── Notifications/
│   └── Providers/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── public/
├── resources/
│   ├── js/
│   └── views/
├── routes/
│   └── web.php
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
├── package.json
├── vite.config.js
├── README.md
├── INSTALACION.md
├── DEPLOY-RAILWAY.md
└── DEPLOYMENT_RAILWAY.md
```

## 6. Modelos principales

### User

Representa a cualquier usuario del sistema. Tiene el rol principal: `admin`, `company` o `student`.

Atributos relevantes:

- `name`
- `email`
- `password`
- `role`
- `google_id`
- `avatar`
- `active`

Relaciones:

- `studentProfile()`
- `company()`
- `applications()`

### Company

Representa la empresa registrada en la plataforma.

Atributos relevantes:

- `company_name`
- `nit`
- `sector`
- `contact_person`
- `phone`
- `address`
- `description`
- `status`

Relaciones:

- `user()`
- `jobPostings()`

### JobPosting

Representa la vacante publicada por una empresa.

Atributos relevantes:

- `title`
- `description`
- `requirements`
- `responsibilities`
- `area`
- `contract_type`
- `modality`
- `location`
- `salary_range`
- `positions`
- `deadline`
- `status`
- `programs_targeted`

Relaciones:

- `company()`
- `applications()`

### Application

Representa una postulación realizada por un estudiante a una vacante.

Atributos relevantes:

- `user_id`
- `job_posting_id`
- `cover_letter`
- `cv_path`
- `status`
- `company_notes`

Estados comunes:

- `pending`
- `reviewed`
- `interview`
- `accepted`
- `rejected`

### StudentProfile

Perfil académico y profesional del estudiante.

Atributos relevantes:

- `student_code`
- `program`
- `semester`
- `phone`
- `about`
- `cv_path`
- `linkedin`

## 7. Flujo principal del sistema

### Registro de empresa

1. La empresa accede a la ruta `/register/empresa`.
2. Completa sus datos básicos y los datos de la empresa.
3. El usuario se crea con rol `company`.
4. La empresa queda en estado `pending` y requiere aprobación del administrador.

### Aprobación administrativa

1. El administrador revisa la empresa desde `/admin/empresas`.
2. Puede aprobarla o rechazarla.
3. Cuando se aprueba, la empresa puede publicar vacantes.

### Publicación de vacantes

1. La empresa autenticada entra al módulo `empresa`.
2. Crea una vacante con:
   - título,
   - descripción,
   - requisitos,
   - responsabilidades,
   - área,
   - modalidad,
   - salario,
   - fecha límite,
   - número de posiciones.
3. La vacante queda activa si cumple validaciones y su fecha no expiró.

### Postulación estudiantil

1. El estudiante inicia sesión con Google o credenciales.
2. Explora la sección de vacantes.
3. Filtra por área, modalidad o contrato.
4. Selecciona una vacante y aplica.
5. El sistema guarda la postulación y notifica a la empresa.

### Gestión de postulaciones

1. La empresa revisa las solicitudes.
2. Actualiza el estado según el proceso.
3. El estudiante puede consultar su historial de aplicaciones.

## 8. Rutas principales

El archivo principal de rutas se encuentra en `routes/web.php`.

### Públicas

- `/` — página inicial / landing page.
- `/login` — login.
- `/register/empresa` — registro de empresa.
- `/auth/google` — autenticación con Google.

### Administrador

- `/admin/dashboard`
- `/admin/empresas`
- `/admin/usuarios`
- `/admin/vacantes`
- `/admin/reportes`

### Empresa

- `/empresa/dashboard`
- `/empresa/perfil`
- `/empresa/vacantes`
- `/empresa/postulaciones`

### Estudiante

- `/estudiante/dashboard`
- `/estudiante/perfil`
- `/estudiante/vacantes`
- `/estudiante/postulaciones`

## 9. Middleware y control de acceso

El sistema usa middleware para restringir rutas por rol:

- `role:admin`
- `role:company`
- `role:student`
- `company.approved`
- `auth`

Esto garantiza que cada usuario solo acceda a las secciones correspondientes a su perfil.

## 10. Base de datos y migraciones

Las migraciones se encuentran en `database/migrations` y gestionan:

- usuarios,
- perfiles estudiantiles,
- empresas,
- vacantes,
- aplicaciones,
- notificaciones.

Es importante ejecutar las migraciones con:

```bash
php artisan migrate
```

Si se requiere poblar datos base, se usa el seeder principal ubicado en `database/seeders/DatabaseSeeder.php`.

## 11. Requisitos de instalación

### Requisitos del entorno

- PHP >= 8.2
- Composer >= 2.x
- MySQL >= 8.0 o MariaDB >= 10.5
- Node.js >= 18 (para desarrollo front-end)
- Extensiones PHP recomendadas:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

## 12. Instalación rápida

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## 13. Variables de entorno

Ejemplo básico de `.env`:

```env
APP_NAME="Bolsa de Empleo UNIPAZ"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bolsa_empleo_unipaz
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=correo@unipaz.edu.co
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=correo@unipaz.edu.co
MAIL_FROM_NAME="Bolsa de Empleo UNIPAZ"

GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

## 14. Autenticación con Google

El sistema usa `laravel/socialite` para permitir el acceso institucional de estudiantes con dominio `@unipaz.edu.co`.

El flujo es:

1. El usuario entra a `/auth/google`.
2. Laravel Socialite redirige a Google.
3. El usuario autoriza el acceso.
4. El backend valida el dominio del correo.
5. El usuario es autenticado y redirigido al panel correspondiente.

## 15. Notificaciones

El proyecto incluye notificaciones del sistema y de correo para eventos como:

- aprobación de empresa,
- nueva postulación,
- cambio de estado de aplicación,
- avisos de acceso o validación.

## 16. Gestión de archivos y CV

El sistema permite cargar documentaciones como:

- CV de estudiante,
- logo de empresa,
- archivos anexos de postulación.

Se recomienda configurar almacenamiento público con:

```bash
php artisan storage:link
```

## 17. Seguridad y buenas prácticas

Algunas medidas integradas en el proyecto incluyen:

- validación de formularios con Laravel,
- restricción por roles,
- aprobación manual de empresas,
- control de usuario activo/inactivo,
- validación de archivos y tipos de documento,
- uso de hashing para contraseñas.

## 18. Comandos útiles

```bash
php artisan serve
php artisan migrate
php artisan migrate:fresh --seed
php artisan route:list
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan storage:link
```

## 19. Despliegue

Para despliegue de producción se recomienda:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

También existen documentos específicos en el repositorio para despliegue en Railway:

- `DEPLOY-RAILWAY.md`
- `DEPLOYMENT_RAILWAY.md`
- `INSTALACION.md`

## 20. Resumen funcional

El proyecto implementa una solución integral para la bolsa de empleo universitaria, conectando a tres actores clave:

- Administración: validación, supervisión y reportes.
- Empresas: publicación y gestión de vacantes.
- Estudiantes: búsqueda, postulación y seguimiento.

## 21. Licencia

El proyecto está configurado bajo licencia MIT, según el valor definido en `composer.json`.

## 22. Referencias dentro del repositorio

- `routes/web.php` — definiciones de rutas.
- `app/Models` — lógica de datos y relaciones.
- `app/Http/Controllers` — controladores por rol.
- `resources/views` — interfaces del sistema.
- `database/migrations` — esquema de la base de datos.
- `INSTALACION.md` — guía de instalación detallada.

## 23. Recomendación final

Para trabajar eficientemente con este proyecto, se recomienda seguir este orden:

1. Configurar `.env` con la base de datos y credenciales de correo.
2. Ejecutar migraciones.
3. Revisar roles y permisos.
4. Probar flujo de empresa, estudiante y administrador.
5. Configurar Google OAuth si se requiere ingreso institucional.
6. Realizar despliegue con las variables y permisos de producción.

Si necesitas, también puedo dejarte una versión aún más detallada orientada a:

- documentación técnica para desarrolladores,
- documentación para usuarios finales,
- o un README más visual y profesional para GitHub.

