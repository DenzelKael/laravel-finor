# Contrato API — Laravel Finor

## 1. Objetivo

Este documento define el contrato estándar para las respuestas JSON de la API de Laravel Finor.

El contrato será utilizado como referencia para los módulos que expongan información mediante JSON y para el consumo del frontend mediante `ApiClient`.

La implementación debe respetar las reglas académicas del proyecto:

- las rutas API deben declararse explícitamente;
- no se permite `Route::resource`;
- no se permite `Route::apiResource`;
- las rutas deben agruparse mediante `Route::controller()`;
- el frontend debe consumir la API únicamente mediante `ApiClient`;
- `ApiClient` utiliza Axios internamente;
- no se permite utilizar `fetch`, `XMLHttpRequest` ni Axios directamente;
- las respuestas de recursos deben utilizar API Resources;
- los endpoints deben escribirse en inglés y plural;
- los campos compuestos deben utilizar `snake_case`.

---

# 2. Versionado y ubicación

Las rutas de API se ubicarán en:

```text
routes/api.php
```

Los endpoints versionados utilizarán el prefijo:

```text
/api/v1
```

Ejemplos:

```text
/api/v1/clients
/api/v1/plans
/api/v1/payments
```

No se utilizarán rutas API nuevas dentro de:

```text
routes/web.php
```

---

# 3. Convención de nombres

## Endpoints

Los recursos deben utilizar nombres:

- en inglés;
- en plural;
- en minúsculas;
- separados con guion cuando corresponda.

Ejemplos válidos:

```text
/api/v1/clients
/api/v1/plans
/api/v1/payments
```

## Campos JSON

Los campos compuestos deben utilizar:

```text
snake_case
```

Ejemplos:

```json
{
    "subscription_id": 10,
    "payment_date": "2026-10-29",
    "payment_method": "cash",
    "status_label": "Registrado"
}
```

---

# 4. Respuesta exitosa de un recurso

Cuando una operación devuelve un recurso individual, se utilizará:

```json
{
    "data": {
        "id": 1
    },
    "message": "Operación realizada correctamente."
}
```

La propiedad:

```text
data
```

debe construirse mediante un API Resource, por ejemplo:

```text
ClientResource
PlanResource
PaymentResource
```

Los datos de recursos no deben construirse manualmente dentro del Controller.

---

# 5. Creación exitosa — 201

Una creación exitosa debe responder:

```http
201 Created
```

Ejemplo:

```json
{
    "data": {
        "id": 15,
        "name": "Juan Pérez",
        "email": "juan@example.com",
        "phone": "70000000",
        "address": "Santa Cruz",
        "status": "ACTIVE",
        "status_label": "Activo"
    },
    "message": "Cliente creado correctamente."
}
```

Regla:

```text
POST que crea un nuevo recurso
→ 201 Created
```

---

# 6. Consulta exitosa — 200

Una consulta exitosa debe utilizar:

```http
200 OK
```

Ejemplo:

```json
{
    "data": {
        "id": 15,
        "name": "Juan Pérez"
    }
}
```

---

# 7. Actualización exitosa — 200

Una actualización exitosa utilizará:

```http
200 OK
```

Ejemplo:

```json
{
    "data": {
        "id": 15,
        "name": "Juan Pérez Actualizado"
    },
    "message": "Cliente actualizado correctamente."
}
```

---

# 8. Colecciones y paginación

Las colecciones utilizarán:

```json
{
    "data": [
        {
            "id": 1
        },
        {
            "id": 2
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 35
    }
}
```

Los elementos de:

```text
data
```

deben transformarse mediante el API Resource correspondiente.

---

# 9. Eliminación exitosa — 204

La eliminación física exitosa utilizará:

```http
204 No Content
```

Un `204` no debe contener cuerpo de respuesta.

Por lo tanto, una eliminación exitosa NO devolverá:

```json
{
    "message": "Eliminado correctamente."
}
```

Respuesta correcta:

```http
HTTP/1.1 204 No Content
```

Esta convención se aplicará de manera uniforme a las eliminaciones de recursos.

---

# 10. Acciones que no son eliminación

Acciones como:

```text
cancel
void
renew
activate
deactivate
```

no se consideran operaciones `DELETE`.

Estas operaciones podrán responder:

```http
200 OK
```

con un mensaje o un recurso actualizado.

Ejemplo:

```json
{
    "message": "Pago anulado correctamente."
}
```

Por tanto:

```text
PATCH /payments/{payment}/cancel
```

no está sujeto a la regla de `204 No Content` definida para `DELETE`.

---

# 11. Error de validación — 422

Cuando los datos enviados no cumplen las reglas de validación:

```http
422 Unprocessable Entity
```

Formato:

```json
{
    "message": "Los datos proporcionados no son válidos.",
    "errors": {
        "email": [
            "El correo ya se encuentra registrado."
        ],
        "phone": [
            "El teléfono es obligatorio."
        ]
    }
}
```

La propiedad:

```text
errors
```

contiene los errores asociados a cada campo.

---

# 12. Regla de negocio no válida — 422

Cuando una operación es válida técnicamente pero no puede realizarse por una regla del dominio, también se utilizará `422` cuando corresponda al comportamiento definido por el proyecto.

Ejemplo:

```json
{
    "message": "No se puede eliminar un cliente con suscripciones.",
    "errors": {
        "client": [
            "El cliente posee suscripciones asociadas."
        ]
    }
}
```

Código:

```http
422 Unprocessable Entity
```

---

# 13. Recurso no encontrado — 404

Cuando el recurso solicitado no existe:

```http
404 Not Found
```

Formato estándar:

```json
{
    "message": "Recurso no encontrado."
}
```

Ejemplo:

```text
GET /api/v1/clients/999999
```

si el cliente no existe:

```http
404 Not Found
```

---

# 14. Usuario no autenticado — 401

Cuando la petición requiere autenticación y el usuario no está autenticado:

```http
401 Unauthorized
```

Formato:

```json
{
    "message": "No autenticado."
}
```

`401` significa que el sistema no puede identificar a un usuario autenticado válido para realizar la petición.

---

# 15. Usuario sin permiso — 403

Cuando el usuario está autenticado pero no posee el permiso requerido:

```http
403 Forbidden
```

Formato:

```json
{
    "message": "No tiene permisos para realizar esta acción."
}
```

Ejemplo:

```text
Usuario autenticado
+
no posee clients.delete
=
403 Forbidden
```

---

# 16. Diferencia entre 401 y 403

```text
401
→ el usuario no está autenticado.

403
→ el usuario está autenticado,
  pero no tiene autorización.
```

---

# 17. API Resources

Las respuestas de entidades deben construirse mediante API Resources.

Resources actuales:

```text
ClientResource
PlanResource
PaymentResource
```

Ejemplo correcto:

```php
return response()->json([
    'data' => new PlanResource($plan),
    'message' => 'Plan creado correctamente.',
], 201);
```

No se debe repetir manualmente la representación del recurso dentro del Controller.

Ejemplo que debe evitarse:

```php
return response()->json([
    'data' => [
        'id' => $plan->id,
        'nombre' => $plan->nombre,
        'precio' => $plan->precio,
    ],
]);
```

La transformación corresponde a:

```text
PlanResource
```

---

# 18. Endpoints previstos — Clients

Base:

```text
/api/v1/clients
```

Operaciones previstas:

| Método | Endpoint | Acción | Respuesta |
|---|---|---|---|
| GET | `/api/v1/clients` | Listar clientes | 200 |
| POST | `/api/v1/clients` | Crear cliente | 201 |
| PUT | `/api/v1/clients/{client}` | Actualizar cliente | 200 |
| DELETE | `/api/v1/clients/{client}` | Eliminar cliente | 204 |

Ejemplo de creación:

```http
POST /api/v1/clients
```

Request:

```json
{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "phone": "70000000",
    "address": "Santa Cruz"
}
```

Respuesta:

```http
201 Created
```

```json
{
    "data": {
        "id": 15,
        "name": "Juan Pérez",
        "email": "juan@example.com",
        "phone": "70000000",
        "address": "Santa Cruz",
        "status": "ACTIVE",
        "status_label": "Activo"
    },
    "message": "Cliente creado correctamente."
}
```

---

# 19. Endpoints previstos — Plans

Base:

```text
/api/v1/plans
```

Operaciones previstas relacionadas con las acciones JSON existentes:

| Método | Endpoint | Acción | Respuesta |
|---|---|---|---|
| POST | `/api/v1/plans` | Crear plan | 201 |
| PUT | `/api/v1/plans/{plan}` | Actualizar plan | 200 |
| DELETE | `/api/v1/plans/{plan}` | Eliminar plan | 204 |

Las respuestas de Plan utilizarán:

```text
PlanResource
```

Ejemplo:

```json
{
    "data": {
        "id": 1,
        "nombre": "Plan mensual",
        "descripcion": "Plan mensual",
        "precio": 100.0,
        "duracion_dias": 30,
        "activo": true
    },
    "message": "Plan creado correctamente."
}
```

---

# 20. Endpoints previstos — Payments

Base:

```text
/api/v1/payments
```

Operaciones relacionadas con las respuestas JSON actuales:

| Método | Endpoint | Acción | Respuesta |
|---|---|---|---|
| POST | `/api/v1/payments` | Registrar pago | 201 |
| PATCH | `/api/v1/payments/{payment}/cancel` | Anular pago | 200 |
| GET | `/api/v1/payments/subscription/{subscription}/history` | Historial por suscripción | 200 |
| GET | `/api/v1/payments/client/{client}/history` | Historial por cliente | 200 |

Los recursos Payment utilizarán:

```text
PaymentResource
```

---

# 21. Rutas explícitas

No se permite:

```php
Route::resource(...);
```

ni:

```php
Route::apiResource(...);
```

Las rutas deben declararse explícitamente.

Ejemplo:

```php
Route::controller(ClientController::class)
    ->prefix('clients')
    ->name('clients.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{client}', 'update')->name('update');
        Route::delete('/{client}', 'destroy')->name('destroy');
    });
```

La versión final deberá incluir los middleware de autenticación y permisos correspondientes.

---

# 22. Consumo desde frontend

Todas las solicitudes del frontend deben pasar por:

```text
ApiClient
```

Ejemplo correcto:

```javascript
import api from '../commons/api-client.js';

const clients = await api.get('/clients');

await api.post('/clients', formData);

await api.delete(`/clients/${id}`);
```

No se permite:

```javascript
fetch('/api/v1/clients');
```

Tampoco:

```javascript
axios.get('/api/v1/clients');
```

El frontend no debe saltarse `ApiClient`.

---

# 23. `/api/chart-data`

Actualmente existe una ruta de dashboard con URL:

```text
/api/chart-data
```

pero se encuentra registrada en:

```text
routes/web.php
```

Su traslado deberá coordinarse con Equipo G.

Este contrato no modifica todavía la ruta.

Antes de realizar el traslado se deberá verificar:

- qué archivo frontend la consume;
- que la petición utilice `ApiClient`;
- si debe mantenerse temporalmente la URL actual;
- cómo evitar romper el dashboard.

La modificación deberá realizarse en un Pull Request separado.

---

# 24. Resumen de códigos HTTP

| Caso | Código |
|---|---:|
| Consulta exitosa | 200 |
| Actualización exitosa | 200 |
| Acción exitosa (`cancel`, `renew`, etc.) | 200 |
| Creación exitosa | 201 |
| Eliminación exitosa | 204 |
| Usuario no autenticado | 401 |
| Usuario sin autorización | 403 |
| Recurso no encontrado | 404 |
| Validación / regla no procesable | 422 |

---

# 25. Criterio de implementación

Este documento define el contrato objetivo.

La implementación del contrato deberá realizarse posteriormente y debe ser revisada con los equipos relacionados antes de modificar comportamiento compartido.

En particular:

```text
Equipo B
→ Resources y controladores relacionados.

Equipo G
→ dashboard y /api/chart-data.

Equipo H
→ integración relacionada con el contrato API.
```

Los cambios funcionales deben realizarse únicamente después de revisar y aprobar este contrato.