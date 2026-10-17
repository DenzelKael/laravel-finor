## Convención de permisos

Los permisos siguen el formato:

`modulo.accion`

Por ejemplo, `clients.view` permite acceder a la visualización de clientes, mientras que `clients.create` permite registrar nuevos clientes.

Las acciones disponibles dependen del módulo. No todos los módulos tienen las mismas acciones.

## Permisos por módulo

### 👥 users

| Permiso        | ¿Qué habilita?                     |
| -------------- | ---------------------------------- |
| `users.view`   | Ver listado y detalle de usuarios  |
| `users.create` | Crear usuarios                     |
| `users.update` | Editar usuarios y asignarles roles |
| `users.delete` | Eliminar usuarios                  |

### 🛡️ roles

| Permiso        | ¿Qué habilita?            |
| -------------- | ------------------------- |
| `roles.view`   | Ver listado de roles      |
| `roles.create` | Crear roles               |
| `roles.update` | Editar rol y sus permisos |
| `roles.delete` | Eliminar roles            |

### 🔑 permissions

| Permiso              | ¿Qué habilita?          |
| -------------------- | ----------------------- |
| `permissions.view`   | Ver listado de permisos |
| `permissions.create` | Crear permisos custom   |
| `permissions.update` | Editar permisos         |
| `permissions.delete` | Eliminar permisos       |

&gt; ⚠️ **Solo Admin.** Ningún otro rol debe recibir `permissions.*`.

### 🧑‍💼 clients

| Permiso          | ¿Qué habilita?                    |
| ---------------- | --------------------------------- |
| `clients.view`   | Ver listado y detalle de clientes |
| `clients.create` | Registrar clientes                |
| `clients.update` | Editar datos del cliente          |
| `clients.delete` | Eliminar cliente                  |

### 📋 plans

| Permiso        | ¿Qué habilita?         |
| -------------- | ---------------------- |
| `plans.view`   | Ver planes disponibles |
| `plans.create` | Crear planes           |
| `plans.update` | Editar planes          |
| `plans.delete` | Eliminar planes        |

### 💳 payments

| Permiso           | ¿Qué habilita?      |
| ----------------- | ------------------- |
| `payments.view`   | Ver pagos y recibos |
| `payments.create` | Registrar un pago   |
| `payments.cancel` | Cancelar un pago    |

&gt; ⚠️ **No existe** `payments.update` ni `payments.delete`. Un pago erróneo se **cancela** y se registra uno nuevo.

### 🔄 subscriptions

| Permiso                | ¿Qué habilita?                     |
| ---------------------- | ---------------------------------- |
| `subscriptions.view`   | Ver suscripciones activas          |
| `subscriptions.create` | Crear suscripción (cliente + plan) |
| `subscriptions.update` | Cambiar plan / fechas              |
| `subscriptions.cancel` | Cancelar suscripción               |
