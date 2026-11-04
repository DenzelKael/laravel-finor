# Manual de Usuario - Sistema Laravel Finor

Guía oficial de operación y administración del sistema de gestión comercial, suscripciones y recaudación **Laravel Finor**.

---

## 1. Introducción y Perfiles de Usuario

El sistema cuenta con un control de acceso basado en roles (**RBAC** con Spatie) diseñado para separar las funciones administrativas de las operativas:

| Rol | Alcance | Módulos Habilitados |
| :--- | :--- | :--- |
| **Administrador (`Admin`)** | Control absoluto del sistema, gestión financiera, auditoría, configuración de usuarios, roles y seguridad. | Dashboard, Clientes, Planes, Suscripciones, Pagos, Reportes, Usuarios, Roles y Permisos. |
| **Vendedor (`Vendedor`)** | Operación diaria comercial, atención al cliente, suscripción a planes y recaudación de caja. | Dashboard, Clientes (creación/edición), Planes (consulta), Suscripciones (alta/renovación), Pagos (registro y recibos) y Reportes de vencimiento. |

---

## 2. Acceso y Seguridad

### 2.1. Inicio de Sesión
1. Ingrese a la URL base del sistema (`/login`).
2. Introduzca su correo electrónico registrado y contraseña.
3. Presione el botón **"Iniciar sesión"**.
4. En caso de credenciales inválidas, el sistema mostrará la notificación correspondiente.

### 2.2. Mi Perfil
- Desde la esquina superior derecha, haga clic en su nombre de usuario para acceder a **"Mi Perfil"** (`/profile`).
- Podrá actualizar sus datos personales (nombre, correo) y cambiar su contraseña de acceso.

### 2.3. Cierre de Sesión
- En la barra superior o menú lateral, seleccione la opción **"Cerrar sesión"** para finalizar su sesión de forma segura.

---

## 3. Dashboard (Panel de Control Principal)

El Dashboard (`/dashboard`) proporciona una visión panorámica e inmediata del estado del negocio en tiempo real.

```
+-----------------------------------------------------------------------------------+
|  [ Total Clientes ]          [ Ingresos del Mes ]        [ Suscripciones por Vencer ] |
|        150                       Bs. 45,850.00                       12               |
+-----------------------------------------------------------------------------------+
|                                                                                   |
|  [Gráfica Interactiva] Resumen Semanal de Ingresos (Chart.js)                     |
|  Recaudación consolidada por semana del año en curso.                            |
|                                                                                   |
+-----------------------------------------------------------------------------------+
```

### Indicadores Principales:
1. **Total Clientes Activos:** Conteo en tiempo real de clientes que cuentan con al menos una suscripción vigente.
2. **Ingresos Mensuales:** Recaudación acumulada neta del mes actual (calculada a partir de los pagos con estado `REGISTERED`).
3. **Suscripciones por Vencer:** Cantidad de clientes cuyas suscripciones vencerán en los próximos 15 días.
4. **Gráfico de Ingresos Semanales:** Gráfica de líneas interactiva que agrupa los ingresos semana a semana consumidos dinámicamente vía API.

---

## 4. Módulo de Clientes

Permite registrar y gestionar la cartera de clientes del gimnasio/servicio.

### 4.1. Listado y Búsqueda (`/clients`)
- **Filtros rápidos:** Ingrese el nombre, correo o teléfono en la barra superior para filtrar de inmediato.
- **Paginación:** Navegue entre páginas conservando los criterios de búsqueda activos.
- **Exportación:** Botón superior para descargar el listado de clientes en formato CSV/Excel.

### 4.2. Registrar Nuevo Cliente (`/clients/create`)
1. Haga clic en **"Nuevo Cliente"**.
2. Complete los campos obligatorios:
   - **Nombre completo** (alfanumérico).
   - **Correo electrónico** (debe ser único en el sistema).
   - **Teléfono / WhatsApp** (contacto directo).
   - **Dirección** (domicilio del cliente).
3. Haga clic en **"Guardar"**. El sistema enviará los datos mediante AJAX (`ApiClient`) y confirmará el registro con una alerta de éxito sin recargar la página.

### 4.3. Editar Cliente
- En la tabla de clientes, presione el botón de edición (icono lápiz) junto al cliente deseado para actualizar su información.

---

## 5. Módulo de Planes

Catálogo de membresías y servicios disponibles para contratación.

### 5.1. Consulta de Planes (`/plans`)
- Visualice la lista de planes activos con su tarifa en Bolivianos (Bs.) y su duración en días.

### 5.2. Crear o Modificar un Plan *(Exclusivo Administrador)*
1. Acceda a **Planes ➔ Nuevo Plan**.
2. Especifique:
   - **Nombre del plan** (ej: *Plan Mensual Musculación*, *Plan Trimestral VIP*).
   - **Descripción:** Beneficios, horarios u observaciones del plan.
   - **Precio (Bs.):** Monto oficial a cobrar.
   - **Duración (días):** Período de vigencia (ej: 30 días, 90 días, 365 días).
   - **Estado Activo:** Casilla para habilitar o inhabilitar su venta al público.
3. Presione **"Guardar"**.

---

## 6. Módulo de Suscripciones

Gestiona el ciclo de vida de la afiliación entre un cliente y un plan.

### 6.1. Alta de Suscripción (`/subscriptions/create`)
1. Ingrese a **Suscripciones ➔ Nueva Suscripción**.
2. Seleccione el **Cliente** del menú desplegable o buscador integrado.
3. Seleccione el **Plan** que desea adquirir.
4. Defina la **Fecha de Inicio** (por defecto, la fecha actual).
5. El sistema calculará automáticamente la **Fecha de Expiración** sumando los días estipulados en el plan.
6. Guarde el formulario. La suscripción quedará en estado `ACTIVE`.

### 6.2. Renovación de Suscripción
- Cuando un cliente solicita continuar su membresía, presione el botón **"Renovar"** en la fila de su suscripción actual.
- El sistema extenderá la fecha de vencimiento según la duración del plan manteniendo la continuidad del servicio.

### 6.3. Cancelación de Suscripción
- Si el cliente solicita la baja definitiva, presione **"Cancelar"**.
- El estado pasará a `CANCELLED`, excluyéndolo de los reportes activos y métricas vigentes.

---

## 7. Módulo de Pagos y Caja

Administra la recaudación financiera, comprobantes y anulaciones.

### 7.1. Registrar un Pago (`/payments/create`)
1. Acceda a **Pagos ➔ Registrar Pago** o utilice el botón directo desde la suscripción del cliente.
2. Seleccione la suscripción a abonar. El sistema mostrará:
   - Monto total del plan.
   - Montos previamente abonados (si existieran).
   - Saldo pendiente de pago.
3. Ingrese el **Monto a cobrar** (Bs.).
4. Seleccione el **Método de Pago**:
   - `Efectivo`: Cobro manual en caja física.
   - `QR`: Pago interoperable (Simple / Banco Central).
   - `Transferencia`: Transferencia bancaria directa.
   - `Tarjeta`: Cobro mediante terminal POS (débito/crédito).
5. Guarde el pago. El estado inicial será `REGISTERED`.

### 7.2. Emisión y Descarga de Recibo
- Inmediatamente registrado el pago, presione **"Ver Recibo"** (`/payments/{id}/receipt`).
- Podrá imprimir el recibo digital con el desglose de fecha, cliente, método y monto, o descargarlo en formato PDF (`/payments/{id}/receipt/pdf`).

### 7.3. Anulación de Pagos *(Regla Financiera)*
> ⚠️ **Importante:** Por auditoría contable no se permite modificar ni borrar pagos físicamente. Únicamente pueden **anularse**.

1. En el historial de pagos, localice el pago a invalidar y presione **"Anular"** (`payments.cancel`).
2. Se solicitará obligatoriamente un **Motivo de Anulación** (ej: *Error de digitación en monto*, *Cancelación inmediata del cliente*).
3. Tras confirmar, el pago pasará al estado `VOIDED`, se registrará la fecha y hora exacta en `voided_at` y su importe se descontará automáticamente de las métricas de ingresos.

---

## 8. Módulo de Reportes: Suscripciones por Vencer

Ubicado en **Reportes ➔ Suscripciones por vencer** (`/reports/expiring-subscriptions`). Herramienta estratégica para la fidelización y cobranza preventiva.

```
+-----------------------------------------------------------------------------------+
|  [ 🔴 Vencen en ≤ 7 días ]    [ 🟡 Vencen en ≤ 15 días ]    [ 🔵 Vencen en ≤ 30 días ]  |
|            45                              98                            210      |
+-----------------------------------------------------------------------------------+
|  Filtro Rápido: [ 7 Días ]  [ 15 Días ]  [ 30 Días ]   | Buscar: [ ____________ ] |
+-----------------------------------------------------------------------------------+
```

### Características Operativas:
1. **Semáforo Visual de Alerta:**
   - 🔴 **Rojo (Peligro):** Suscripciones que expiran en **7 días o menos** (atención inmediata).
   - 🟡 **Amarillo (Advertencia):** Suscripciones que expiran en **8 a 15 días** (aviso preventivo al cliente).
   - 🔵 **Azul (Informativo):** Suscripciones que expiran en **16 a 30 días** (planificación mensual).
2. **Acción Rápida de Cobro:** Cada registro incluye un botón directo de **"Pagar"** para renovar al cliente en un solo clic.

---

## 9. Módulo de Configuración y Seguridad *(Solo Administrador)*

Ubicado en la sección **SISTEMA ➔ Configuración** del menú lateral:

### 9.1. Usuarios (`/admin/users`)
- Alta de operadores del sistema asignándoles su rol correspondiente (`Admin` o `Vendedor`).

### 9.2. Roles y Permisos (`/admin/roles` y `/admin/permissions`)
- Creación de perfiles de usuario personalizados con selección modular de permisos granulares (`*.view`, `*.create`, `*.update`, `*.delete`).
- Los cambios en roles y permisos vacían la caché de Spatie al instante para asegurar su aplicación inmediata.

---

## 10. Respuestas del Sistema y Manejo de Errores

El sistema incorpora interfaces en español amigables adaptadas al diseño de AdminLTE para cualquier eventualidad:

- **403 - Acceso Denegado:** Si un usuario intenta acceder a una URL para la cual su rol no tiene permiso concedido. Incluye botón para volver al inicio.
- **404 - No Encontrado:** Cuando la página o recurso consultado no existe o fue dado de baja.
- **419 - Sesión Expirada:** Cuando el token de seguridad CSRF caducó por inactividad. Indica recargar la página e iniciar sesión nuevamente.
- **500 - Error del Servidor:** Pantalla limpia que no expone datos técnicos sensibles al usuario final.

