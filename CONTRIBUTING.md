# Guía de contribución – Laravel Finor

## 1. Introducción

Este documento establece las normas y recomendaciones que deben seguir los integrantes del equipo durante el desarrollo colaborativo de Laravel Finor.

El objetivo es mantener una estructura organizada, evitar conflictos entre los integrantes y facilitar la revisión e integración de los cambios.

## 2. Requisitos previos

Antes de comenzar a trabajar, asegúrate de tener instalado:

- PHP compatible con el proyecto.
- Composer.
- Node.js y npm.
- Git.
- Visual Studio Code u otro editor de código.
- Laragon o un entorno local compatible.

También debes tener acceso al repositorio de GitHub.

## 3. Organización de ramas

Cada integrante debe trabajar en una rama específica para su tarea. No se deben realizar directamente los cambios de desarrollo en `main`.

Se recomienda utilizar los siguientes prefijos:

| Prefijo | Uso | Ejemplo |
|---|---|---|
| `feature/` | Nuevas funcionalidades | `feature/modulo-clientes` |
| `fix/` | Corrección de errores | `fix/error-suscripciones` |
| `docs/` | Documentación | `docs/readme-contributing` |
| `test/` | Pruebas | `test/clientes` |

Para crear una rama:

```bash
git switch main
git pull origin main
git switch -c feature/nombre-de-la-tarea
```

Sustituye el nombre de la rama por el correspondiente a tu tarea.

## 4. Convenciones de desarrollo

Durante el desarrollo se deben respetar las siguientes recomendaciones:

- Mantener la estructura existente del proyecto.
- Utilizar nombres descriptivos para clases, métodos y variables.
- Evitar duplicar código cuando exista una funcionalidad reutilizable.
- Separar las responsabilidades de cada módulo.
- Validar los datos recibidos desde el backend.
- Comprobar los permisos antes de ejecutar operaciones protegidas.
- Mantener una comunicación constante con el equipo.

### 4.1. Convenciones de rutas

Las rutas deben declararse explícitamente.

**No está permitido utilizar:**

```php
Route::resource();
Route::apiResource();
```

Tampoco deben utilizarse URL que contengan `resources`.

Se deben definir las operaciones mediante rutas individuales, respetando las convenciones existentes del proyecto.

## 5. Uso de componentes compartidos

Para mantener la uniformidad del frontend, se deben reutilizar los componentes disponibles:

- **ApiClient:** centraliza las peticiones HTTP mediante Axios.
- **BaseForm:** facilita el envío de formularios y la gestión de errores de validación.
- **SwalMessages:** centraliza las alertas y confirmaciones mediante SweetAlert2.

Antes de crear una funcionalidad similar, revisa si alguno de estos componentes ya resuelve esa necesidad.

## 6. Convenciones para commits (Conventional Commits)

Todos los integrantes deben utilizar el estándar **Conventional Commits** para escribir mensajes de commit claros, consistentes y fáciles de identificar.

### 6.1. Estructura del mensaje

Los mensajes deben seguir esta estructura:

```text
<tipo>(<alcance>): <descripción>
```

Donde:

- **tipo:** identifica la naturaleza del cambio.
- **alcance:** indica el módulo o parte del proyecto afectada. Es opcional.
- **descripción:** resume brevemente el cambio realizado.

Ejemplos:

```text
feat(suscripciones): agregar registro de suscripciones
fix(clientes): corregir validación del correo electrónico
docs: actualizar guía de instalación
test(planes): agregar pruebas de creación de planes
```

### 6.2. Tipos de commits permitidos

Se utilizarán los siguientes tipos:

| Tipo | Uso |
|---|---|
| `feat` | Incorporación de una nueva funcionalidad. |
| `fix` | Corrección de un error. |
| `docs` | Cambios en la documentación. |
| `style` | Cambios de formato que no modifican la lógica. |
| `refactor` | Modificación interna del código sin cambiar su comportamiento. |
| `test` | Incorporación o modificación de pruebas. |
| `build` | Cambios relacionados con dependencias o sistema de compilación. |
| `ci` | Cambios en la integración y ejecución automatizada de pruebas. |
| `perf` | Mejoras de rendimiento. |
| `chore` | Tareas de mantenimiento que no corresponden a los tipos anteriores. |

### 6.3. Reglas para escribir los mensajes

- Utilizar minúsculas en el tipo y en la descripción.
- Escribir una descripción breve y específica.
- Utilizar el modo imperativo para describir el cambio.
- No finalizar la descripción con un punto.
- Evitar mensajes genéricos como `cambios`, `actualización` o `correcciones`.
- Utilizar el alcance cuando ayude a identificar el módulo afectado.

### 6.4. Cambios incompatibles (Breaking Changes)

Cuando un commit introduzca un cambio que rompa la compatibilidad con funcionalidades existentes, se debe indicar utilizando `!` después del tipo o tipo y alcance.

Ejemplo:

```text
feat(api)!: modificar estructura de respuesta
```

También se puede explicar el cambio incompatible en el cuerpo del mensaje utilizando el pie `BREAKING CHANGE:`.

```text
feat(api): modificar estructura de respuesta

BREAKING CHANGE: los consumidores deben actualizar
la forma en que interpretan la respuesta de la API.
```

### 6.5. Ejemplos de commits para Laravel Finor

```bash
git commit -m "feat(suscripciones): agregar registro de suscripciones"
git commit -m "fix(clientes): corregir validación de correo"
git commit -m "docs: actualizar CONTRIBUTING"
git commit -m "test(planes): agregar pruebas de planes"
git commit -m "refactor(usuarios): simplificar controlador"
```

Todos los integrantes deben respetar esta convención para mantener un historial de cambios organizado y facilitar la revisión del proyecto.

## 7. Flujo de trabajo con Git

Antes de comenzar:

```bash
git status
git switch main
git pull origin main
git switch -c feature/nombre-de-la-tarea
```

Durante el desarrollo, revisa los archivos modificados:

```bash
git status
git diff
```

Cuando los cambios estén listos:

```bash
git add .
git commit -m "feat: descripcion del cambio"
git push -u origin feature/nombre-de-la-tarea
```

Después, crea un Pull Request en GitHub para que los demás integrantes puedan revisar los cambios.

**Importante:** evita utilizar `git add .` si tienes archivos ajenos a tu tarea. Revisa siempre `git status` antes de preparar los cambios.

## 8. Pruebas y revisión

Antes de enviar un Pull Request, se recomienda ejecutar:

```bash
php artisan test
```

También se deben comprobar:

- Que las rutas funcionen correctamente.
- Que las validaciones sean correctas.
- Que los permisos estén aplicados.
- Que las relaciones entre modelos funcionen.
- Que los formularios gestionen correctamente los errores.
- Que no se hayan incorporado credenciales ni archivos sensibles.

## 9. Migraciones y base de datos

Cuando una tarea requiera modificar la estructura de la base de datos:

- Crear una migración específica.
- Utilizar nombres descriptivos.
- Comprobar las relaciones y claves foráneas.
- Coordinar los cambios con los demás integrantes.
- No ejecutar migraciones destructivas sin autorización y sin comprobar sus consecuencias.

No se deben compartir archivos `.env` ni credenciales reales.

## 10. Pull Requests

Cada Pull Request debe incluir:

1. Una descripción clara del objetivo.
2. Los cambios realizados.
3. Los módulos o archivos afectados.
4. Las pruebas ejecutadas.
5. Capturas de pantalla cuando sean útiles para revisar cambios visuales.

Los integrantes deben revisar los cambios antes de integrarlos en `main`.

## 11. Seguridad

- No subir contraseñas, tokens ni claves privadas.
- No compartir archivos `.env`.
- Validar los datos en el backend.
- Verificar la autorización de cada operación protegida.
- Comunicar vulnerabilidades de manera responsable y privada.

## 12. Consideraciones finales

La colaboración organizada permite mantener un código más claro, facilitar el mantenimiento y reducir los conflictos durante el desarrollo.

Todos los integrantes deben respetar estas recomendaciones y mantener actualizada su rama de trabajo.