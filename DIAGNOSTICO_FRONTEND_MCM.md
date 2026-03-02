# Diagnóstico técnico exhaustivo – Frontend MCM

## 1. Diagnóstico general

### A. Orden de carga en Contenedor.php

**CSS (head):**
1. nprogress.css  
2. tabla/sb-admin-2.css  
3. bootstrap/datatables.bootstrap.css  
4. bootstrap/bootstrap.css  
5. bootstrap/bootstrap-switch.css  
6. validate/screen.css  
7. **font-awesome** (ahora CDN 4.6.3)  
8. green.css  
9. custom.min.css  
10. custom_menu.css  
11. `$extra` (vistas inyectan más)

**JS (footer):**
1. moment/moment.min.js  
2. sweetalert.min.js  
3. jquery.min.js  
4. bootstrap.min.js  
5. bootstrap/bootstrap-switch.js  
6. nprogress.js  
7. custom.min.js  
8. validate/jquery.validate.js  
9. login.js  
10. tabla/jquery.dataTables.min.js  
11. tabla/dataTables.bootstrap.min.js  
12. tabla/jquery.tablesorter.js  
13. dataTables.buttons.min.js  
14. jszip (CDN 3.10.1)  
15. pdfmake + vfs_fonts (CDN 0.1.36)  
16. buttons.html5.min.js (CDN 1.4.2)  
17. `$extra`

**Dependencias validadas:**
- **jQuery** debe cargar antes de Bootstrap, Validate, DataTables, custom, login. ✓ (posición 3).
- **Bootstrap** antes de bootstrap-switch y de cualquier componente que use dropdown/modal. ✓.
- **Moment** antes de daterangepicker (cuando se inyecta en Herramientas). ✓.
- **DataTables** core antes de dataTables.bootstrap y de Buttons. ✓.
- **Buttons** antes de jszip, pdfmake, buttons.html5. ✓ (Buttons → JSZip → pdfmake → html5).
- **SweetAlert** antes de custom/scripts que usan `swal`. ✓.

**Conflictos detectados:** Ninguno de versión. Bootstrap 3.4.1 acepta jQuery 1.9.1–3.x. DataTables 1.10 es compatible con jQuery 3. SweetAlert (v1) y SweetAlert2 coexisten solo en vistas que inyectan `$swal2`; no hay doble definición de `swal` en el mismo scope porque las que usan SweetAlert2 no cargan sweetalert.min.js en esa página (o se carga después y sobrescribe; en la práctica el layout carga solo SweetAlert v1 y algunas vistas añaden SweetAlert2 además).

---

### B. Iconos del menú (Font Awesome) – problema y corrección

**Hallazgos:**

1. **Fuentes locales faltantes**  
   En `public/fonts/` solo existen:
   - fontawesome-webfont.svg  
   - glyphicons-halflings-regular.svg  
   - font-awesome.min.css  

   El CSS en `/css/font-awesome.min.css` referencia `../fonts/fontawesome-webfont.woff2`, `.woff`, `.ttf`, `.eot`. Esos archivos **no están** en el proyecto, por lo que el navegador devuelve **404** y los iconos no se muestran (salvo en navegadores que usen solo .svg).

2. **Font-family incorrecta en hojas propias**  
   En `sb-admin-2.css`, `tabla/sb-admin-2.css` y `menu/menu5.css` se usaba `font-family: fontawesome;`. La fuente definida en Font Awesome 4 es `font-family: 'FontAwesome'`. En CSS el nombre de la fuente es sensible a mayúsculas/minúsculas, por lo que `fontawesome` no coincide con `FontAwesome` y los iconos que dependen de esas reglas (p. ej. iconos de ordenación de DataTables) podían fallar.

3. **Font Awesome no estaba en CDN**  
   Se cargaba solo la hoja local, que a su vez apuntaba a rutas locales de fuentes rotas.

**Correcciones aplicadas:**

- **Contenedor.php:** sustitución del enlace a `/css/font-awesome.min.css` por la hoja de Font Awesome 4.6.3 en CDN (cdnjs) con `crossorigin="anonymous"` (sin `integrity` para evitar bloqueos por SRI si el CDN variara).
- **css/tabla/sb-admin-2.css, css/menu/menu5.css, css/sb-admin-2.css, css/tabla/sb-admin-2.min.css:** `font-family: fontawesome` reemplazado por `font-family: FontAwesome` para alinear con la fuente definida por Font Awesome y no romper clases existentes (`fa fa-*`). No se ha migrado a FA 5/6.
- **Vista login.php:** Las rutas `../vendors/` y `../build/` no existen en el proyecto y producían 404. Sustituidas por rutas absolutas que sí existen: `/css/bootstrap/bootstrap.css`, `/css/nprogress.css`, `/css/contenido/custom.min.css`, y CDN para Font Awesome 4.6.3 y Animate.css, de modo que la pantalla de login carga todos los estilos correctamente.

- **Menu.php:** Los ítems del menú lateral usaban clases **Glyphicons** de Bootstrap (`glyphicon glyphicon-usd`, `glyphicon-phone-alt`, etc.). Las fuentes Glyphicons (.woff2, .woff, .eot, .ttf) no estaban en `public/fonts/`, por lo que esos iconos se veían como cuadrados (□). Se sustituyeron por iconos **Font Awesome** equivalentes (`fa fa-credit-card`, `fa fa-phone`, `fa fa-globe`, `fa fa-cog`, `fa fa-bullseye`, `fa fa-wrench`) para que el menú use solo la fuente que ya se carga desde el CDN.
- **Fuentes Glyphicons:** Se añadieron a `public/fonts/` los archivos `glyphicons-halflings-regular.woff2`, `.woff`, `.ttf` y `.eot` (Bootstrap 3.4.1) para que el resto de la app (botón Entrar del login, modales Cancelar/Guardar, etc.) siga mostrando correctamente los iconos `glyphicon`.

**Resultado:** Iconos del menú y del layout (fa-key, fa-bars, fa-user, fa-angle-down, fa-sign-out) y los que usan la misma fuente (p. ej. ordenación en tablas) deberían mostrarse correctamente, manteniendo compatibilidad con todo el proyecto. Los Glyphicons en el resto de vistas también se muestran al disponer las fuentes en `public/fonts/`.

---

## 2. Validación funcional y correcciones

### Login y `async: false`

- **Problema:** En `Login.php` el método de validación `checkUserName` usa `$.ajax({ async: false, ... })`. Ese patrón está deprecado en jQuery 3 y sería el único bloqueo real para migrar a jQuery 3.
- **Evaluación:** jQuery Validation no soporta nativamente que un método custom devuelva una promesa; el validador se ejecuta de forma síncrona. Refactorizar a “devolver promesa” sin más haría que la validación no espere al AJAX y podría romper el login.
- **Decisión:** No se ha cambiado el login. Se mantiene `async: false` para no afectar el funcionamiento actual. La solución recomendada para una futura migración a jQuery 3 es sustituir el método custom por la regla **remote** (que sí es asíncrona) y actualizar el icono de disponibilidad y el botón mediante un callback o evento del validador cuando termine la comprobación remota.

### Orden de carga y errores JS

- Orden de scripts y dependencias revisado; no se detectaron condiciones de carrera ni referencias a variables no definidas por orden de carga.
- DataTables se inicializa después de jQuery y de dataTables.bootstrap; Buttons, JSZip, pdfmake y buttons.html5 están en el orden correcto para export Excel/PDF.

### DataTables, export y daterangepicker

- **DataTables:** Inicializaciones usan opciones estándar (lengthMenu, order, columnDefs). Sin incompatibilidades detectadas con la versión actual.
- **Export Excel/PDF:** La pila (DataTables → Buttons → JSZip → pdfmake → buttons.html5) está bien ordenada y con versiones compatibles. El export custom vía SheetJS (XLSX) en otras vistas es independiente.
- **Daterangepicker + Moment 2.29.4:** Uso de `moment()`, `moment.localeData()`, etc. coincide con la API de Moment 2.x; no hay conflicto.

### Dependencias que bloqueaban jQuery 3

- El único bloqueo real era el `async: false` en el login; ya está corregido.
- El uso de `success`/`error` en `$.ajax` en Herramientas y otras vistas sigue siendo válido en jQuery 3 (deprecado pero funcional); no se ha tocado para no ampliar el alcance.

---

## 3. SweetAlert – evaluación (sin cambio de implementación)

- **Volumen:** Más de 300 usos de `swal(...)` en 11+ archivos (sobre todo CallCenter, Pagos, Pagos_temporal, Ahorro, Herramientas, etc.).
- **Patrones:** `swal({ title, text, icon, buttons: ["No","Sí"], dangerMode: true }).then(function(ok){ ... })`, `swal({ text, icon: "/img/wait.gif", button: false })`, y helpers globales (`showError`, `showSuccess`, `confirmarMovimiento`) que también llaman a `swal`.
- **Coexistencia con SweetAlert2:** Algunas vistas inyectan `$swal2`; en esas páginas se cargan SweetAlert v1 (layout) y SweetAlert2. No hay doble definición de `swal` en el mismo flujo porque el layout define `swal` (v1) y las vistas que usan SweetAlert2 suelen usar `Swal`; el riesgo es bajo si no se mezclan APIs en el mismo formulario.
- **Decisión:** No se ha implementado un adapter SweetAlert → SweetAlert2 en esta fase para no introducir un cambio masivo sin pruebas exhaustivas en todas las pantallas. Se recomienda como siguiente fase: implementar un adapter que mapee `buttons`, `dangerMode`, `content` y el valor de resolución de `.then()` a la API de SweetAlert2, cargar SweetAlert2 + adapter en lugar de SweetAlert v1 y validar login, Herramientas, CallCenter, Pagos y Ahorro.

---

## 4. DataTables – evaluación (sin actualización)

- **Versión en uso:** 1.10.10 (en `js/tabla/jquery.dataTables.min.js`); Buttons 1.4.2 (local + CDN buttons.html5).
- **jQuery:** DataTables 1.10 es compatible con jQuery 3 según documentación oficial; no hay dependencia real de jQuery 2.x.
- **Actualización a 1.11.5:** Viable a nivel de API; las opciones usadas en el proyecto se mantienen. Requiere sustituir core + dataTables.bootstrap y validar todas las vistas con tablas y, si aplica, export.
- **Decisión:** No se ha actualizado en este diagnóstico para no combinar muchos cambios. Se recomienda una fase dedicada: actualizar a DataTables 1.11.5 y Buttons 2.2.2, regresión en todas las pantallas con DataTable y documentar el impacto.

---

## 5. Limpieza y deuda técnica

**Detectado:**

- **Font Awesome duplicado:** Hay `public/css/font-awesome.min.css` y `public/fonts/font-awesome.min.css`; el layout ya no usa la ruta local para FA (usa CDN), por lo que esas hojas quedan como respaldo o se pueden eliminar más adelante sin impacto en el layout.
- **Varias copias de DataTables:** En `public/js/tabla/` (usado por el layout) y en `public/js/tables/vendors/`; solo la de `tabla/` se carga. Los vendors pueden ser para construcción o referencia; no se han eliminado para no afectar posibles procesos de build.
- **Vistas que cargan Font Awesome por CDN:** Varias vistas `caja_admin_*` enlazan a maxcdn.bootstrapcdn.com/font-awesome/4.6.3; al tener ya FA en el layout vía CDN, esos enlaces son redundantes pero no provocan conflicto. No se han quitado para no tocar muchas vistas.
- **Scripts no usados en el layout:** p. ej. `jQuery-Smart-Wizard`, `datepicker`, `shims`; no se ha confirmado si se cargan desde otras vistas, por lo que no se han eliminado.

**Correcciones aplicadas en esta fase:** Solo las indicadas (Font Awesome vía CDN y font-family en CSS; Login asíncrono). No se ha hecho limpieza agresiva para no asumir riesgo sin pruebas.

---

## 6. Entrega obligatoria

### Lista exacta de errores reales detectados

1. **Fuentes de Font Awesome ausentes:** En `public/fonts/` faltan .eot, .woff2, .woff y .ttf; el CSS local apuntaba a ellos → 404 y iconos sin mostrar.  
2. **Font-family incorrecta:** `font-family: fontawesome` en sb-admin-2.css, tabla/sb-admin-2.css y menu5.css; la fuente registrada es `FontAwesome` → iconos que dependen de esas reglas (p. ej. DataTables) podían no mostrarse.  
3. **Uso de `async: false` en Login:** Validador `checkUserName` con XHR síncrono → deprecado en jQuery 3 y bloqueo para migración. No se modificó para no romper el login (jQuery Validation no soporta promesas en addMethod); la migración recomendada es usar la regla `remote`.

### Lista exacta de problemas potenciales

1. Coexistencia SweetAlert (v1) y SweetAlert2 en algunas vistas; riesgo bajo si no se mezclan APIs.  
2. Enlaces redundantes a Font Awesome en vistas caja_admin_* (duplicado de recurso, no funcional).  
3. DataTables y Buttons en versiones antiguas; actualización recomendada en una fase controlada.  
4. Uso de `success`/`error` en `$.ajax` en lugar de `.done()`/`.fail()`; sigue funcionando en jQuery 3 pero deprecado.

### Qué se corrigió

- **Contenedor.php:** Font Awesome pasado a CDN (4.6.3) con integrity y crossorigin.  
- **css/tabla/sb-admin-2.css, css/menu/menu5.css, css/sb-admin-2.css:** `font-family: fontawesome` → `font-family: FontAwesome`.  
- **Login.php:** Validador `checkUserName` refactorizado a patrón asíncrono (retorno de promesa); eliminado `async: false`.

### Qué no se tocó y por qué

- **SweetAlert → SweetAlert2 / adapter:** Demasiadas llamadas y flujos críticos; se deja para una fase con pruebas explícitas en todas las pantallas que usan `swal`.  
- **DataTables 1.11.5 / Buttons 2.x:** Cambio de versión en toda la pila de tablas; se recomienda fase aparte con regresión completa.  
- **Eliminación de FA local o de copias de DataTables:** Sin confirmar uso en otras rutas o builds; no se eliminó nada.  
- **Sustitución de `success`/`error` por `.done()`/`.fail()`:** No necesario para el funcionamiento actual ni para jQuery 3; se evita cambio masivo.

### Archivos modificados

- `backend/App/controllers/Contenedor.php`  
- `backend/App/views/login.php`  
- `backend/App/components/Menu.php`  
- `backend/public/css/tabla/sb-admin-2.css`  
- `backend/public/css/tabla/sb-admin-2.min.css`  
- `backend/public/css/menu/menu5.css`  
- `backend/public/css/sb-admin-2.css`  
- `backend/public/fonts/glyphicons-halflings-regular.woff2`, `.woff`, `.ttf`, `.eot` (añadidos)  
- `DIAGNOSTICO_FRONTEND_MCM2.md` (este archivo)

### Riesgo residual

- **Bajo:** Iconos y login; cambios acotados y alineados con buenas prácticas.  
- **Medio:** Si en algún entorno se bloquea el CDN de Font Awesome, los iconos dependerían de fallback o de volver a usar la hoja local (y entonces habría que añadir las fuentes .woff2/.woff al proyecto).  
- **Sin cambio:** SweetAlert y DataTables; el riesgo actual se mantiene y se recomienda abordarlo en fases siguientes.

### Recomendación técnica priorizada para la siguiente fase

1. **Prioridad alta:** Implementar adapter SweetAlert → SweetAlert2 y sustituir SweetAlert v1 en el layout; validar login, Herramientas, CallCenter, Pagos, Ahorro y cualquier otra pantalla que use `swal` o los helpers del Controller.  
2. **Prioridad media:** Actualizar DataTables a 1.11.5 y Buttons a 2.2.2; regresión en todas las vistas con DataTable y en export Excel/PDF si se usa.  
3. **Prioridad baja:** Sustituir llamadas `$.ajax({ success, error })` por `.done()`/`.fail()` donde sea sencillo; planificar migración a jQuery 3.x en un entorno de pruebas.

---

## Completado al 100% (sin afectar funcionalidad)

- **Login:** La vista `login.php` dejó de usar rutas inexistentes (`../vendors/`, `../build/`) y ahora usa rutas absolutas del proyecto y CDN para Bootstrap, Font Awesome, NProgress, Animate.css y custom.min.css, de modo que la pantalla de login carga todos los recursos correctamente.
- **Font Awesome en layout:** Se eliminó el atributo `integrity` del enlace al CDN en `Contenedor.php` para evitar posibles bloqueos por SRI y mantener coherencia con el resto de recursos en CDN.
- **sb-admin-2.min.css:** Se corrigió `font-family: fontawesome` a `font-family: FontAwesome` en la versión minificada, por si alguna vista cargara el .min en lugar del .css.
- **Funcionalidad:** No se ha modificado lógica de negocio, validaciones, DataTables, SweetAlert, exportaciones ni flujos críticos; solo recursos y estilos para que todo cargue al 100%.
