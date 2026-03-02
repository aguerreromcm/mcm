# Inventario y actualización de librerías frontend – Proyecto MCM2

## 1. Inventario de dependencias actuales

### 1.1 Layout principal (`App\controllers\Contenedor.php`)

| Recurso | Ruta actual | Versión detectada | Uso |
|--------|-------------|-------------------|-----|
| jQuery | `/js/jquery.min.js` | 2.1.4 | Base de la aplicación |
| Bootstrap CSS | `/css/bootstrap/bootstrap.css` | 3.3.7 | Layout, grid, componentes |
| Bootstrap JS | `/js/bootstrap.min.js` | 3.3.5 | Dropdowns, modales, collapse |
| Bootstrap Switch | `/js/bootstrap/bootstrap-switch.js` | - | Switches en formularios |
| jQuery Validation | `/js/validate/jquery.validate.js` | 1.15.0 | Validación de formularios (login, etc.) |
| SweetAlert | `/js/sweetalert.min.js` | v1 (sweetalert) | Alertas y confirmaciones en toda la app |
| Moment.js | `/js/moment/moment.min.js` | 2.9.0 | Fechas; dependencia de daterangepicker |
| DataTables | `/js/tabla/jquery.dataTables.min.js` | 1.10.10 | Tablas con búsqueda, orden, paginación |
| DataTables Bootstrap | `/js/tabla/dataTables.bootstrap.min.js` | - | Estilos Bootstrap para DataTables |
| TableSorter | `/js/tabla/jquery.tablesorter.js` | - | Orden en algunas tablas |
| DataTables Buttons | `/js/dataTables.buttons.min.js` | 1.4.2 | Botones Excel/PDF/Print |
| JSZip | CDN cdnjs 3.1.3 | 3.1.3 | Export Excel (Buttons) |
| pdfmake | CDN cdnjs 0.1.32 | 0.1.32 | Export PDF (Buttons) |
| Buttons HTML5 | CDN datatables.net 1.4.2 | 1.4.2 | Implementación HTML5 de export |

### 1.2 Recursos inyectados por controladores

| Recurso | Dónde | Uso |
|---------|--------|-----|
| SweetAlert2 | `Core\Controller::$swal2`; inyectado en Ahorro, etc. | Alertas en módulo Ahorro |
| Socket.io | `Core\Controller::$socket` | Comunicación en tiempo real |
| daterangepicker | Herramientas (header + script) | Rango de fechas en Auditoría Devengo |
| Chart.js | RadarCobranza (CDN) | Gráficas |
| XLSX (SheetJS) | AdminSucursales, Ahorro (CDN) | Export Excel alternativo |
| Chart.min.js | Indicadores (local) | Gráficas |

### 1.3 Puntos de uso críticos detectados

- **Login**: `jquery.min.js`, `jquery.validate.js`, `$("#login").validate({...})`, regla custom `checkUserName`.
- **DataTables**: `$("#...").DataTable({...})`, `$.fn.DataTable.isDataTable()`, `.DataTable().clear().destroy()`, en Herramientas y vistas de auditoría.
- **Alertas**: Más de 14 archivos usan `swal({...})` (SweetAlert 1). Varias vistas de Ahorro cargan además SweetAlert2.
- **Export**: Orden de carga en footer: Buttons → JSZip (CDN) → pdfmake (CDN) → buttons.html5 (CDN). Debe mantenerse.
- **Daterangepicker**: Depende de `moment` y `jQuery`; usa `$(element).daterangepicker(cfg, callback)`.

---

## 2. Decisiones de actualización

### 2.1 Actualizaciones aplicadas (bajo riesgo, compatibilidad mantenida)

| Librería | Versión anterior | Versión nueva | Justificación |
|----------|------------------|---------------|----------------|
| jQuery | 2.1.4 | 2.2.4 | Última 2.x; API compatible; correcciones de seguridad y bugs. |
| Bootstrap (CSS + JS) | 3.3.x | 3.4.1 | Última 3.x; solo parches; sin cambio de API. |
| jQuery Validation | 1.15.0 | 1.19.5 | Misma API pública; correcciones y mejoras. |
| Moment.js | 2.9.0 | 2.29.4 | Mantenimiento 2.x; API estable para el uso actual. |
| JSZip (CDN) | 3.1.3 | 3.10.1 | Misma API para uso con DataTables Buttons. |
| pdfmake (CDN) | 0.1.32 | 0.1.36 | Última 0.1.x en cdnjs; API de documento estable. |
| URLs CDN | `//cdn...` | `https://cdn...` | Forzar HTTPS por seguridad. |

### 2.2 No actualizado (y motivo)

| Recurso | Motivo |
|---------|--------|
| DataTables 1.10.10 / Buttons 1.4.2 | Actualizar a 1.11 + Buttons 2.x exigiría validar todas las tablas y exports. Riesgo medio sin beneficio inmediato; se deja para una fase posterior con pruebas dedicadas. |
| SweetAlert → SweetAlert2 | La app usa masivamente `swal({...})` (SweetAlert 1). Unificar en SweetAlert2 implica cambiar muchas llamadas y el helper `tipoMensaje` en Controller. Riesgo alto de romper alertas; no se hace en esta fase. |
| Socket.io cliente | Versión acoplada al servidor; actualizar solo el cliente puede generar incompatibilidad. No se toca sin plan servidor+cliente. |
| Daterangepicker | Funciona con moment 2.x y jQuery 2.x. Actualizar solo daterangepicker podría introducir cambios de API; se deja como está. |
| Bootstrap 4/5 | Cambio de clases y de JS; afecta todas las vistas. Refactor grande; excluido explícitamente. |
| jQuery 3.x | Requiere revisar deprecated (p. ej. success/error en $.ajax). Mejor en una iteración con pruebas amplias. |
| NProgress, Bootstrap Switch, TableSorter, custom.min.js, login.js | No se ha detectado versión crítica ni CVE; no se modifican para minimizar riesgo. |

---

## 3. Riesgos remanentes

1. **Dos sistemas de alertas (SweetAlert y SweetAlert2)**  
   Siguen conviviendo. En páginas que inyectan `$swal2` se cargan ambos. Posible conflicto menor de estilos o orden de ejecución; no se ha observado en el código.

2. **DataTables y Buttons en versiones antiguas**  
   Siguen recibiendo parches de seguridad en la rama 1.10/1.4. En un futuro se recomienda planificar salto a 1.11.x + Buttons 2.x con pruebas de todas las pantallas con tablas y export.

3. **Moment.js en modo mantenimiento**  
   El proyecto recomienda migrar a Day.js o date-fns. Daterangepicker y cualquier otro uso de `moment` seguirían funcionando; la migración sería un trabajo aparte.

4. **Font Awesome 4.6.3 (CDN en vistas caja_admin_*)**  
   No se ha cambiado; sigue siendo 4.x. La migración a FA 5/6 implica cambios de clases (por ejemplo `fa` → `fas`/`far`).

---

## 4. Archivos modificados (lista exacta)

- `backend/App/controllers/Contenedor.php`  
  - CDN: jszip 3.1.3 → 3.10.1, pdfmake 0.1.32 → 0.1.36, protocolo `https:` en todos los CDN del footer.

- `backend/public/js/jquery.min.js`  
  - Reemplazado por jQuery 2.2.4 (min).

- `backend/public/js/bootstrap.min.js`  
  - Reemplazado por Bootstrap 3.4.1 (min).

- `backend/public/css/bootstrap/bootstrap.css`  
  - Reemplazado por Bootstrap 3.4.1 (min) manteniendo la ruta para no tocar referencias en PHP.

- `backend/public/js/validate/jquery.validate.js`  
  - Reemplazado por jQuery Validation 1.19.5 (min).

- `backend/public/js/moment/moment.min.js`  
  - Reemplazado por Moment 2.29.4 (min).

---

## 5. Comprobaciones recomendadas tras el cambio

- [ ] Login: validación de usuario, mensajes de error, envío del formulario.
- [ ] Una o varias pantallas con DataTables: búsqueda, ordenación, paginación.
- [ ] Export a Excel y a PDF desde una tabla con botones.
- [ ] Herramientas → Auditoría Devengo: daterangepicker y filtros de fecha.
- [ ] Cualquier pantalla que use `swal(...)` (confirmaciones, “Procesando...”, etc.).
- [ ] Módulo Ahorro (vistas que cargan SweetAlert2): flujos que muestran alertas.
- [ ] Consola del navegador: ausencia de errores y de deprecations críticas.

---

## 6. Nota sobre CDN

Los scripts de JSZip y pdfmake usan `https://` y `crossorigin="anonymous"` para cargar desde CDN de forma segura. No se usan atributos `integrity` (SRI) para evitar fallos de carga si el CDN sirve una variante distinta del recurso.
