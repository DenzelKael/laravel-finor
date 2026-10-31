# Diagrama Entidad-Relación (ER) - Laravel Finor

Este documento contiene la especificación del modelo de base de datos relacional para el sistema de gestión de suscripciones y pagos **Laravel Finor**, generado a partir de las migraciones finales de la base de datos.

---

## Diagrama ER en Mermaid

```mermaid
erDiagram
    CLIENTS ||--o{ SUBSCRIPTIONS : "posee (1:N)"
    PLANS ||--o{ SUBSCRIPTIONS : "define (1:N)"
    SUBSCRIPTIONS ||--o{ PAYMENTS : "genera (1:N)"

    USERS ||--o{ MODEL_HAS_ROLES : "asignado_en"
    ROLES ||--o{ MODEL_HAS_ROLES : "asigna_a"
    ROLES ||--o{ ROLE_HAS_PERMISSIONS : "contiene"
    PERMISSIONS ||--o{ ROLE_HAS_PERMISSIONS : "concedido_en"
    USERS ||--o{ MODEL_HAS_PERMISSIONS : "posee_directo"
    PERMISSIONS ||--o{ MODEL_HAS_PERMISSIONS : "otorgado_a"

    CLIENTS {
        bigint id PK "Clave primaria auto-incremental"
        string name "Nombre completo del cliente"
        string email UK "Correo electrónico único"
        string phone "Teléfono de contacto (opcional)"
        string address "Dirección física (opcional)"
        string status "Estado: active, inactive"
        timestamp created_at "Fecha de registro"
        timestamp updated_at "Última actualización"
    }

    PLANS {
        bigint id PK "Clave primaria auto-incremental"
        string nombre UK "Nombre único del plan"
        text descripcion "Detalle de beneficios (opcional)"
        decimal precio "Costo monetario del plan (8,2)"
        int duracion_dias "Duración de vigencia en días"
        boolean activo "Disponibilidad comercial (default true)"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Última actualización"
    }

    SUBSCRIPTIONS {
        bigint id PK "Clave primaria auto-incremental"
        bigint client_id FK "Referencia a clients(id) [restrict]"
        bigint plan_id FK "Referencia a plans(id) [restrict]"
        date start_date "Fecha de inicio de vigencia"
        date expiration_date "Fecha de expiración calculada"
        string status "Estado: ACTIVE, EXPIRED, CANCELLED"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Última actualización"
    }

    PAYMENTS {
        bigint id PK "Clave primaria auto-incremental"
        bigint subscription_id FK "Referencia a subscriptions(id) [restrict]"
        decimal amount "Monto pagado en Bs (10,2)"
        string payment_method "Método: Cash, QR, Transfer, Card"
        date payment_date "Fecha efectiva del pago"
        string status "Estado: REGISTERED, VOIDED"
        string void_reason "Motivo de anulación (opcional)"
        timestamp voided_at "Fecha y hora de anulación"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Última actualización"
    }

    USERS {
        bigint id PK "Clave primaria auto-incremental"
        string name "Nombre del operador o administrador"
        string email UK "Correo corporativo único"
        timestamp email_verified_at "Fecha de verificación de correo"
        string password "Contraseña encriptada con Bcrypt"
        string remember_token "Token de sesión persistente"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Última actualización"
    }

    ROLES {
        bigint id PK "Clave primaria de rol"
        string name "Nombre del rol (Admin, Vendedor)"
        string guard_name "Guard de autenticación (web)"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Última actualización"
    }

    PERMISSIONS {
        bigint id PK "Clave primaria de permiso"
        string name "Nombre del permiso (modulo.accion)"
        string guard_name "Guard de autenticación (web)"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Última actualización"
    }

    ROLE_HAS_PERMISSIONS {
        bigint permission_id FK "Referencia a permissions(id)"
        bigint role_id FK "Referencia a roles(id)"
    }

    MODEL_HAS_ROLES {
        bigint role_id FK "Referencia a roles(id)"
        string model_type "Modelo asignado (App\\Models\\User)"
        bigint model_id FK "ID del usuario referenciado"
    }

    MODEL_HAS_PERMISSIONS {
        bigint permission_id FK "Referencia a permissions(id)"
        string model_type "Modelo asignado (App\\Models\\User)"
        bigint model_id FK "ID del usuario referenciado"
    }
```

---

## Diccionario de Entidades y Cardinalidades

### 1. Dominio de Negocio (Core)

| Relación | Tipo | Restricción de Integridad | Descripción |
| :--- | :---: | :--- | :--- |
| **`clients` ➔ `subscriptions`** | `1 : N` | `ON DELETE RESTRICT` | Un cliente puede tener múltiples suscripciones a lo largo del tiempo, pero una suscripción pertenece a un único cliente. |
| **`plans` ➔ `subscriptions`** | `1 : N` | `ON DELETE RESTRICT` | Un plan puede aplicarse a muchas suscripciones; la suscripción referencia un único plan que determina precio y días. |
| **`subscriptions` ➔ `payments`** | `1 : N` | `ON DELETE RESTRICT` / `CASCADE UPDATE` | Una suscripción puede recibir uno o más abonos/pagos registrados hasta cubrir el saldo. Un pago está asociado a una única suscripción. |

### 2. Dominio de Seguridad y Acceso (Spatie RBAC)

| Relación | Tipo | Descripción |
| :--- | :---: | :--- |
| **`users` ➔ `roles`** | `N : M` | Mediante la tabla pivote polimórfica `model_has_roles`. |
| **`roles` ➔ `permissions`** | `N : M` | Mediante la tabla pivote `role_has_permissions`. |
| **`users` ➔ `permissions`** | `N : M` | Mediante `model_has_permissions` (para asignaciones directas sin pasar por rol). |

---

## Enumeraciones y Valores Permitidos

### 1. `SubscriptionStatus` (`app/Enums/SubscriptionStatus.php`)
- `ACTIVE`: Suscripción vigente dentro del período de inicio y vencimiento.
- `EXPIRED`: Suscripción cuya fecha límite ha pasado.
- `CANCELLED`: Suscripción interrumpida manualmente por el operador.

### 2. `PaymentStatus` (`app/Enums/PaymentStatus.php`)
- `REGISTERED`: Pago válido asentado en caja.
- `VOIDED`: Pago anulado por error administrativo, registrando `void_reason` y marca temporal en `voided_at`.

### 3. `PaymentMethod` (`app/Enums/PaymentMethod.php`)
- `CASH`: Pago en efectivo en caja física.
- `QR`: Pago inmediato mediante código QR interoperable.
- `TRANSFER`: Transferencia bancaria directa.
- `CARD`: Pago con tarjeta de débito o crédito.
