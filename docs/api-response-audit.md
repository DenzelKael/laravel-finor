# Relevamiento actual de respuestas JSON — Equipo F

## Objetivo

Documentar el comportamiento actual de las respuestas JSON de los módulos **Clients**, **Plans** y **Payments** antes de definir y aplicar un contrato único de API.

Este documento describe el estado actual. No propone todavía cambios funcionales.

---

## 1. Estado general

Actualmente el proyecto no dispone de un archivo:

```text
routes/api.php
```

Las rutas funcionales de Clients, Plans y Payments se encuentran en:

```text
routes/web.php
```

Además, la ruta:

```text
/api/chart-data
```

también se encuentra declarada en `routes/web.php`, a pesar de utilizar el prefijo `/api`.

Actualmente tampoco existe una convención `/api/v1/...` implementada.

---

# 2. Clients

Controlador:

```text
app/Http/Controllers/ClientController.php
```

Resource:

```text
app/Http/Resources/ClientResource.php
```

## Listado JSON

Cuando `ClientController@index` recibe una petición que espera JSON:

```php
return ClientResource::collection($clients);
```

La respuesta utiliza `ClientResource`.

Código HTTP esperado:

```text
200 OK
```

El resultado está paginado.

---

## Crear cliente

Método:

```text
ClientController@store
```

Respuesta actual:

```json
{
    "data": {
        "id": 1,
        "name": "Cliente",
        "email": "cliente@example.com",
        "phone": "70000000",
        "address": "Dirección",
        "status": "ACTIVE",
        "status_label": "Activo"
    },
    "message": "Cliente creado exitosamente."
}
```

Código HTTP:

```text
201 Created
```

Implementación:

```php
return (new ClientResource($client))
    ->additional([
        'message' => 'Cliente creado exitosamente.'
    ])
    ->response()
    ->setStatusCode(201);
```

### Observación

Esta operación utiliza `ClientResource` para construir los datos de respuesta.

---

## Actualizar cliente

Método:

```text
ClientController@update
```

Respuesta actual:

```json
{
    "data": {
        "...": "..."
    },
    "message": "Cliente actualizado exitosamente."
}
```

Código HTTP:

```text
200 OK
```

La respuesta utiliza `ClientResource`.

---

## Eliminar cliente

Método:

```text
ClientController@destroy
```

Respuesta exitosa actual:

```json
{
    "data": null,
    "message": "Cliente eliminado exitosamente."
}
```

Código HTTP:

```text
200 OK
```

Si el cliente posee suscripciones:

```json
{
    "data": null,
    "message": "No se puede eliminar un cliente con suscripciones."
}
```

Código HTTP:

```text
422 Unprocessable Entity
```

### Observación

La operación de eliminación utiliza una estructura distinta de las operaciones create/update.

---

# 3. Plans

Controlador:

```text
app/Http/Controllers/PlanController.php
```

Resource:

```text
app/Http/Resources/PlanResource.php
```

## Crear plan

Método:

```text
PlanController@store
```

Respuesta actual:

```json
{
    "message": "Plan creado correctamente.",
    "data": {
        "id": 1,
        "nombre": "Plan",
        "descripcion": "Descripción",
        "precio": 100.0,
        "duracion_dias": 30,
        "activo": true
    }
}
```

Código HTTP:

```text
201 Created
```

Los datos son transformados mediante:

```php
new PlanResource($plan)
```

---

## Actualizar plan

Método:

```text
PlanController@update
```

Respuesta actual:

```json
{
    "message": "Plan actualizado correctamente.",
    "data": {
        "...": "..."
    }
}
```

Código HTTP:

```text
200 OK
```

Los datos utilizan `PlanResource`.

---

## Eliminar plan

Método:

```text
PlanController@destroy
```

Respuesta exitosa actual:

```json
{
    "message": "Plan eliminado correctamente."
}
```

Código HTTP:

```text
200 OK
```

Si el plan posee suscripciones:

```json
{
    "message": "El plan tiene suscripciones; desactívelo en lugar de eliminarlo."
}
```

Código HTTP:

```text
422 Unprocessable Entity
```

### Observación

A diferencia de Clients, la eliminación de Plans no contiene:

```json
{
    "data": null
}
```

Por lo tanto, actualmente ambas operaciones de eliminación utilizan contratos distintos.

---

# 4. Payments

Controlador:

```text
app/Http/Controllers/PaymentController.php
```

Resource:

```text
app/Http/Resources/PaymentResource.php
```

## Registrar pago

Método:

```text
PaymentController@store
```

Respuesta actual:

```json
{
    "data": {
        "id": 1,
        "subscription_id": 1,
        "amount": 100.0,
        "formatted_amount": "...",
        "payment_date": "2026-10-29",
        "payment_method": "...",
        "payment_method_label": "...",
        "status": "...",
        "status_label": "...",
        "void_reason": null,
        "voided_at": null,
        "created_at": "..."
    },
    "message": "Pago registrado correctamente."
}
```

Código HTTP:

```text
201 Created
```

Los datos utilizan:

```php
new PaymentResource($payment)
```

---

## Anular pago

Método:

```text
PaymentController@cancel
```

Respuesta actual:

```json
{
    "message": "Pago anulado correctamente."
}
```

Código HTTP:

```text
200 OK
```

### Observación

La respuesta no contiene propiedad `data`, a diferencia del registro de pagos.

---

## Historial por suscripción

Método:

```text
PaymentController@historyBySubscription
```

Cuando la petición espera JSON, la estructura actual es:

```json
{
    "data": [],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 0
    }
}
```

Código HTTP:

```text
200 OK
```

Los elementos de `data` utilizan `PaymentResource`.

---

## Historial por cliente

Método:

```text
PaymentController@historyByClient
```

Respuesta JSON actual:

```json
{
    "data": [],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 0
    }
}
```

Código HTTP:

```text
200 OK
```

Los elementos utilizan `PaymentResource`.

---

# 5. Matriz de respuestas actuales

| Módulo | Operación | Respuesta | HTTP |
|---|---|---|---|
| Clients | Listar | Resource collection paginada | 200 |
| Clients | Crear | `data + message` | 201 |
| Clients | Actualizar | `data + message` | 200 |
| Clients | Eliminar | `data:null + message` | 200 |
| Clients | Error al eliminar | `data:null + message` | 422 |
| Plans | Crear | `message + data` | 201 |
| Plans | Actualizar | `message + data` | 200 |
| Plans | Eliminar | `message` | 200 |
| Plans | Error al eliminar | `message` | 422 |
| Payments | Registrar | `data + message` | 201 |
| Payments | Anular | `message` | 200 |
| Payments | Historial suscripción | `data + meta` | 200 |
| Payments | Historial cliente | `data + meta` | 200 |

---

# 6. API Resources existentes

Actualmente existen:

```text
ClientResource
PlanResource
PaymentResource
```

`ClientResource` define:

```text
id
name
email
phone
address
status
status_label
```

`PlanResource` define:

```text
id
nombre
descripcion
precio
duracion_dias
activo
```

`PaymentResource` define:

```text
id
subscription_id
amount
formatted_amount
payment_date
payment_method
payment_method_label
status
status_label
void_reason
voided_at
created_at
```

Todos los nombres compuestos actuales utilizan `snake_case`.

---

# 7. Manejo actual de errores

No existe todavía un contrato JSON único documentado para:

```text
422 Validation Error
404 Not Found
401 Unauthenticated
403 Forbidden
```

Las validaciones de los Form Requests utilizan el comportamiento de Laravel.

Los errores 401/403 dependen actualmente de autenticación, middleware y Policies.

Los errores 404 producidos por Route Model Binding también utilizan el manejo general de Laravel.

Además existen respuestas 422 específicas construidas manualmente en algunos controladores para reglas de negocio.

Por lo tanto, el formato de estas respuestas deberá definirse en:

```text
docs/api-contract.md
```

antes de modificar el comportamiento existente.

---

# 8. Ubicación actual de rutas

Actualmente Clients, Plans y Payments están registrados explícitamente en:

```text
routes/web.php
```

Se cumple la regla académica de no utilizar:

```text
Route::resource
Route::apiResource
```

Las rutas se encuentran agrupadas mediante:

```php
Route::controller(...)
    ->prefix(...)
    ->name(...)
    ->group(...);
```

---

# 9. Hallazgo: `/api/chart-data`

Actualmente existe:

```php
Route::get('/api/chart-data', [DashboardController::class, 'chartData'])
    ->middleware('permission:payments.view');
```

dentro de:

```text
routes/web.php
```

Por lo tanto, aunque la URL utiliza `/api`, técnicamente sigue perteneciendo al grupo de rutas web.

Antes de moverla debe coordinarse con Equipo G para evitar romper el dashboard.

---

# 10. Hallazgos principales

El relevamiento identifica las siguientes diferencias que deberán resolverse en tickets posteriores:

- No existe un contrato JSON único para todas las operaciones.
- Las eliminaciones de Clients y Plans utilizan estructuras distintas.
- Crear utiliza correctamente `201 Created`.
- Las eliminaciones actualmente utilizan `200 OK`; todavía no se ha definido si el contrato final utilizará `200` o `204`.
- Los errores `422`, `404`, `401` y `403` no poseen todavía un formato documentado único.
- Existen `ClientResource`, `PlanResource` y `PaymentResource`, pero el estilo de composición de las respuestas varía entre controladores.
- No existe actualmente `routes/api.php`.
- `/api/chart-data` se encuentra incorrectamente ubicado en `routes/web.php`.
- No existe todavía versionado de endpoints del tipo `/api/v1/...`.
- Los campos compuestos existentes utilizan `snake_case`.

---

# 11. Próximo paso

El siguiente ticket debe definir:

```text
docs/api-contract.md
```

con un contrato estándar para:

```text
Éxito
201 Created
200 OK
204 No Content

422 Unprocessable Entity
404 Not Found
401 Unauthenticated
403 Forbidden
```

La implementación de dicho contrato deberá realizarse únicamente después de su revisión y coordinación con los equipos involucrados.