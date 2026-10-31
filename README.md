# Laravel Finor

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

Laravel Finor es una aplicación web desarrollada con Laravel para la gestión de clientes, planes, suscripciones, pagos, usuarios y permisos. El proyecto se desarrolla de manera colaborativa, utilizando módulos, componentes reutilizables y control de versiones con Git.

## Tabla de contenidos

- [Acerca de Laravel Finor](#acerca-de-laravel-finor)
- [Tecnologías utilizadas](#tecnologías-utilizadas)
- [Requisitos](#requisitos)
- [Instalación y configuración](#instalación-y-configuración)
- [Base de datos y seeders](#base-de-datos-y-seeders)
- [Módulos del sistema](#módulos-del-sistema)
- [Roles y permisos](#roles-y-permisos)
- [Cómo reutilizar ApiClient, BaseForm y SwalMessages](#cómo-reutilizar-apiclient-baseform-y-swalmessages)
- [Ejecución del proyecto](#ejecución-del-proyecto)
- [Pruebas](#pruebas)
- [Actualización del proyecto](#actualización-del-proyecto)
- [Contribuciones al proyecto](#contribuciones-al-proyecto)
- [Acerca del framework Laravel](#acerca-del-framework-laravel)
- [Learning Laravel](#learning-laravel)
- [Agentic Development](#agentic-development)
- [Código de conducta](#código-de-conducta)
- [Seguridad](#seguridad)
- [Licencia](#licencia)

## Acerca de Laravel Finor

Laravel Finor organiza las funcionalidades del sistema en módulos para facilitar el desarrollo, mantenimiento y colaboración entre los integrantes del equipo.

Sus objetivos incluyen:

- Gestionar clientes, planes y suscripciones.
- Administrar usuarios, roles y permisos.
- Organizar las operaciones relacionadas con pagos.
- Reutilizar componentes JavaScript compartidos.
- Mantener las peticiones HTTP y los formularios AJAX con una estructura común.
- Aplicar pruebas y buenas prácticas de control de versiones.

## Tecnologías utilizadas

| Tecnología | Uso |
|---|---|
| PHP | Lenguaje del backend. |
| Laravel 13 | Framework principal. |
| Composer | Gestión de dependencias PHP. |
| MySQL o SQLite | Base de datos, según la configuración del entorno. |
| JavaScript | Interactividad del frontend. |
| Axios | Cliente HTTP utilizado por ApiClient. |
| SweetAlert2 | Alertas y confirmaciones. |
| Vite y npm | Gestión y compilación de recursos frontend. |
| AdminLTE | Componentes de interfaz administrativa. |
| Spatie Laravel Permission | Administración de roles y permisos. |
| Pest | Pruebas automatizadas. |
| Git y GitHub | Control de versiones y colaboración. |

## Requisitos

Antes de instalar el proyecto, comprueba que tienes:

- PHP compatible con `composer.json` (actualmente `^8.3`).
- Composer.
- Node.js y npm.
- Git.
- Una base de datos compatible con la configuración elegida.
- Un editor de código, como Visual Studio Code.

En Windows puedes utilizar Laragon para preparar el entorno local.

## Instalación y configuración

### 1. Descargar el proyecto

```bash
git clone https://github.com/DenzelKael/laravel-finor.git
cd laravel-finor
```

Si ya tienes el proyecto descargado, accede a su carpeta local y continúa.

### 2. Instalar las dependencias

```bash
composer install
npm install
```

### 3. Configurar el entorno

Si todavía no tienes un archivo `.env`, ejecuta:

```bash
cp .env.example .env
```

Genera la clave de Laravel:

```bash
php artisan key:generate
```

### 4. Configurar la base de datos

Edita `.env` y configura la conexión que utilizarás.

El archivo `.env.example` del proyecto utiliza SQLite como configuración predeterminada. Si eliges SQLite, comprueba que exista el archivo de base de datos configurado. Si eliges MySQL, indica correctamente el host, puerto, nombre de base de datos, usuario y contraseña.

### 5. Ejecutar las migraciones

Cuando la conexión esté configurada:

```bash
php artisan migrate
```

Para cargar también los datos iniciales, consulta la sección de seeders antes de ejecutar `php artisan migrate --seed`.

### 6. Compilar el frontend

```bash
npm run build
```

## Base de datos y seeders

Las migraciones definen la estructura de las tablas, mientras que los seeders permiten insertar datos iniciales para el desarrollo.

Ejecutar migraciones pendientes:

```bash
php artisan migrate
```

Ejecutar los seeders:

```bash
php artisan db:seed
```

Ejecutar ambos procesos:

```bash
php artisan migrate --seed
```

El seeder principal coordina la carga de roles y permisos, clientes, planes y suscripciones, según el código existente.

**Precaución:** revisa los seeders antes de ejecutarlos en una base de datos que contenga información importante. No utilices comandos destructivos como `migrate:fresh` sin confirmar que puedes perder los datos existentes.

## Módulos del sistema

| Módulo | Descripción |
|---|---|
| Usuarios, roles y permisos | Gestión de usuarios y autorización de operaciones. |
| Clientes | Administración de los datos de clientes. |
| Planes | Gestión de los planes disponibles. |
| Suscripciones | Relación entre clientes y planes, fechas y estado de las suscripciones. |
| Pagos y facturación | Operaciones relacionadas con pagos. |
| API REST | Integración mediante endpoints, según la implementación disponible. |
| Dashboard y reportes | Presentación de información y consultas. |
| QA y flujo de Git | Pruebas, documentación y colaboración. |

La tabla refleja la organización funcional del proyecto; no implica que todos los módulos tengan el mismo grado de implementación.

## Roles y permisos

El proyecto utiliza Spatie Laravel Permission para organizar la autorización.

Los roles iniciales definidos en los seeders incluyen:

- **Admin:** recibe todos los permisos cargados por el seeder.
- **Vendedor:** recibe permisos específicos para las operaciones asignadas a ese rol.

Los permisos definidos incluyen operaciones relacionadas con usuarios, roles, permisos, clientes, planes, pagos y suscripciones.

La autorización debe comprobarse en el backend mediante los mecanismos correspondientes. Ocultar una opción en el frontend no sustituye la verificación del servidor.

## Cómo reutilizar ApiClient, BaseForm y SwalMessages

Laravel Finor dispone de herramientas JavaScript compartidas para evitar duplicar la lógica de las peticiones HTTP, los formularios AJAX y las notificaciones.

### 1. ApiClient

**Archivo:** `resources/js/commons/api-client.js`

`ApiClient` centraliza las peticiones HTTP mediante Axios. Configura el encabezado CSRF, solicita respuestas JSON y transforma los errores HTTP en objetos `ApiError`.

Importación:

```javascript
import ApiClient from '../commons/api-client.js';
```

Métodos disponibles:

- `get(url, config)`: consulta información.
- `post(url, body, config)`: envía datos.
- `put(url, body, config)`: actualiza información.
- `patch(url, body, config)`: realiza una actualización parcial.
- `delete(url, config)`: solicita la eliminación de un registro.

Ejemplo basado en el módulo de suscripciones:

```javascript
import ApiClient from '../commons/api-client.js';

async function loadSubscriptions() {
    try {
        const subscriptions = await ApiClient.get('/subscriptions/data');
        console.log(subscriptions);
    } catch (error) {
        console.error('No se pudieron cargar las suscripciones:', error);
    }
}
```

En las operaciones `PUT` y `PATCH`, cuando se recibe un `FormData`, el cliente utiliza `POST` con el campo `_method` correspondiente.

Se recomienda reutilizar esta instancia compartida en lugar de crear peticiones Axios independientes en cada módulo.

### 2. BaseForm

**Archivo:** `resources/js/commons/base-form.js`

`BaseForm` permite reutilizar el flujo común de creación y edición mediante formularios AJAX.

Entre sus responsabilidades se encuentran:

- Obtener los campos mediante `FormData`.
- Enviar los datos utilizando `ApiClient`.
- Limpiar los errores anteriores.
- Mostrar errores de validación junto a los campos correspondientes.
- Gestionar errores de autenticación, permisos y registros inexistentes.
- Guardar el mensaje de éxito en `sessionStorage` y redirigir a la URL indicada por el formulario.

Ejemplo de inicialización:

```javascript
import BaseForm from '../commons/base-form.js';

const form = document.getElementById('create-form');

if (form) {
    new BaseForm(form);
}
```

El formulario debe incluir los atributos que necesita la clase:

```html
<form
    id="create-form"
    data-store-url="/ruta-de-creacion"
    data-index-url="/ruta-de-listado"
>
    <!-- Campos del formulario -->
</form>
```

Las rutas del ejemplo son ilustrativas y deben sustituirse por las rutas reales definidas en Laravel.

En formularios de edición, configura también `data-update-url`.

Los campos que muestren errores deben respetar la estructura de elementos que busca `BaseForm`, incluidos `.form-group` e `.invalid-feedback`, cuando corresponda.

Antes de duplicar su lógica, revisa si puedes reutilizar o extender la clase.

### 3. SwalMessages

**Archivo:** `resources/js/commons/swal-messages.js`

`SwalMessages` centraliza las alertas y confirmaciones mediante SweetAlert2.

Importación:

```javascript
import SwalMessages from '../commons/swal-messages.js';
```

Mostrar un mensaje de éxito:

```javascript
SwalMessages.success('La operación se realizó correctamente.');
```

Mostrar un error:

```javascript
SwalMessages.error('No se pudo completar la operación.');
```

Mostrar una advertencia:

```javascript
SwalMessages.warning('El registro ya no existe.');
```

Solicitar confirmación:

```javascript
const confirmed = await SwalMessages.confirm(
    '¿Deseas continuar?',
    'Confirmar operación'
);

if (!confirmed) {
    return;
}
```

Confirmar una eliminación:

```javascript
const confirmed = await SwalMessages.confirmDelete('Este cliente');

if (!confirmed) {
    return;
}

// Continuar con la eliminación confirmada.
```

Métodos disponibles:

- `success(message)`: muestra un mensaje de éxito.
- `error(message)`: muestra un mensaje de error.
- `warning(message)`: muestra una advertencia.
- `confirm(text, title, confirmText)`: solicita confirmación.
- `confirmDelete(entityName)`: solicita confirmación para eliminar un registro.

### 4. Buenas prácticas

- Utiliza `ApiClient` para las peticiones HTTP del frontend.
- Reutiliza `BaseForm` cuando el formulario siga el flujo común de creación o edición.
- Utiliza `SwalMessages` para mantener consistentes las notificaciones.
- Comprueba que las URL utilizadas en JavaScript coincidan con las rutas reales.
- Mantén las validaciones y la autorización en el backend.
- Evita duplicar funcionalidades que ya resuelven las herramientas compartidas.

## Ejecución del proyecto

Para iniciar el entorno de desarrollo configurado en `composer.json`:

```bash
composer run dev
```

Este comando ejecuta los servicios definidos en el script de desarrollo del proyecto, incluidos el servidor de Laravel, el proceso de colas y Vite.

## Pruebas

El proyecto utiliza Pest para las pruebas automatizadas.

Para ejecutar las pruebas:

```bash
php artisan test
```

Antes de enviar cambios, revisa las pruebas pertinentes y comprueba:

- Las validaciones del backend.
- Las rutas y los permisos.
- Las relaciones entre modelos.
- Los casos de éxito y error de los formularios.
- Las operaciones que modifican o eliminan datos.

## Actualización del proyecto

Antes de actualizar tu copia local, revisa el estado de Git:

```bash
git status
```

Guarda tus cambios mediante un commit o resguárdalos antes de integrar modificaciones nuevas.

Para actualizar la rama principal:

```bash
git switch main
git pull origin main
```

Después, vuelve a tu rama de trabajo:

```bash
git switch nombre-de-tu-rama
```

Integra los cambios de `main` siguiendo el procedimiento acordado por el equipo. Resuelve los conflictos y ejecuta las pruebas antes de enviar una solicitud de cambios.

No sobrescribas el trabajo de otros integrantes ni ejecutes migraciones destructivas sin revisar sus consecuencias.

## Contribuciones al proyecto

Consulta [CONTRIBUTING.md](CONTRIBUTING.md) para conocer las normas de colaboración de Laravel Finor.

Como mínimo:

1. Trabaja en una rama específica para tu tarea.
2. Mantén los cambios enfocados en el objetivo asignado.
3. Respeta las convenciones existentes.
4. Ejecuta las pruebas pertinentes.
5. Describe claramente los cambios realizados.
6. Envía una solicitud de cambios para revisión.

Las rutas deben definirse explícitamente. De acuerdo con las convenciones del equipo, no se deben utilizar `Route::resource` ni `Route::apiResource`. Tampoco se deben incluir URL con `resources` en su ruta.

## Acerca del framework Laravel

Laravel es un framework para desarrollar aplicaciones web con una sintaxis expresiva y herramientas integradas para tareas comunes.

Entre sus funcionalidades se encuentran:

- Un sistema de enrutamiento rápido y flexible.
- Un contenedor de inyección de dependencias.
- Soporte para sesiones y caché.
- Eloquent, su ORM para trabajar con bases de datos.
- Migraciones para gestionar cambios en el esquema de datos.
- Procesamiento de trabajos en segundo plano.
- Difusión de eventos en tiempo real.

Laravel es accesible, potente y ofrece herramientas para construir aplicaciones robustas.

## Learning Laravel

Laravel dispone de documentación y recursos de aprendizaje:

- [Documentación oficial de Laravel](https://laravel.com/docs)
- [Laracasts](https://laracasts.com), con tutoriales de Laravel, PHP, pruebas y JavaScript.
- [Laravel Learn](https://laravel.com/learn), con lecciones y proyectos prácticos.

## Agentic Development

La estructura y las convenciones de Laravel permiten trabajar con herramientas de desarrollo asistido por inteligencia artificial.

Laravel Boost puede instalarse en proyectos compatibles para proporcionar herramientas y contexto a los agentes de programación:

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Consulta la [documentación de Laravel sobre desarrollo asistido por IA](https://laravel.com/docs/ai) antes de instalar o configurar esta herramienta.

## Código de conducta

El equipo debe mantener una comunicación respetuosa, colaborar en la resolución de problemas y ofrecer revisiones constructivas.

Para conocer las recomendaciones generales del framework, consulta el [Código de conducta de Laravel](https://laravel.com/docs/contributions#code-of-conduct).

## Seguridad

Si detectas una vulnerabilidad de seguridad en Laravel Finor, no publiques credenciales ni detalles que faciliten su explotación en un issue público.

Comunica el problema de forma responsable a los mantenedores del proyecto mediante el canal privado establecido por el equipo.

No incluyas archivos `.env`, contraseñas, tokens, claves privadas ni credenciales reales en el repositorio.

Para consultar las recomendaciones del framework, visita la [documentación oficial de seguridad de Laravel](https://laravel.com/docs/security).

## Licencia

La licencia aplicable a Laravel Finor debe comprobarse en los archivos de licencia del repositorio.

Laravel es software de código abierto distribuido bajo la licencia MIT. Esta información corresponde al framework y no determina automáticamente la licencia de Laravel Finor ni la de todas sus dependencias.

---

**Laravel Finor**
Creado por Ing. ZED.
