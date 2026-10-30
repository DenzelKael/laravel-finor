## Tarjeta / issue

<!-- Tarjeta de Trello que realiza esta PR y, si corresponde, el issue: "Closes #N" -->

## Qué cambia

<!-- Resumen corto: qué hace y por qué. -->

## Cómo probarlo

<!-- Pasos o tests que lo verifican. -->

## Checklist

- [ ] **SRP**: el controlador solo recibe y responde; la lógica vive en Service/FormRequest/Resource.
- [ ] **Nombres**: clases, métodos y variables en inglés y descriptivos; textos de la interfaz en español.
- [ ] **Tests**: Feature tests del caso feliz, validación (422) y permisos (403); `php artisan test` en verde.
- [ ] **Formato**: ejecuté `vendor/bin/pint` antes del push.
- [ ] **Rutas explícitas**: sin `Route::resource` ni `apiResource`, con middleware `permission:modulo.*`.
- [ ] **AJAX**: solo vía `ApiClient`; formularios con `BaseForm` y mensajes con `SwalMessages` (sin `alert()`, `fetch` ni `axios` directos).
- [ ] **Autorización**: `authorize()` en los FormRequest y permisos nuevos agregados al seeder.
- [ ] **Commits** con Conventional Commits (`feat:`, `fix:`, `test:`, `refactor:`, `style:`, `docs:`).
- [ ] La rama parte de `main` actualizado y no incluye cambios ajenos a la tarjeta.
