# Ejecutar MCM en local

## Requisitos

- **PHP** 7.4 o superior (con extensión `pdo_oci` para Oracle).
- **Oracle** (base de datos). Sin una base configurada, la app cargará pero mostrará "Sistema fuera de línea" al usar cualquier pantalla que requiera BD.

## 1. Configuración

Edita el archivo:

`backend/App/config/configuracion.ini`

y rellena al menos la sección `[database]` con tu servidor Oracle:

```ini
[database]
SERVIDOR             = localhost   ; o la IP/host de tu Oracle
PUERTO               = 1521
USUARIO              = tu_usuario
PASSWORD             = tu_password
ESQUEMA              = ESIACOM
```

(Opcional: `[correo]` y `[APIs]` si vas a usar envío de correo o APIs externas.)

## 2. Arrancar el servidor

Desde la raíz del proyecto (`mcm`):

```bash
cd backend/public
php -S localhost:8000 router.php
```

En Windows (PowerShell o CMD), con PHP en el PATH:

```powershell
cd backend\public
php -S localhost:8000 router.php
```

Si PHP está en `C:\Program Files\Git\bin` o en otra ruta, usa la ruta completa, por ejemplo:

```powershell
& "C:\Program Files\PHP\v8.x\php.exe" -S localhost:8000 router.php
```

(dentro de `backend\public`.)

## 3. Abrir en el navegador

Abre:

**http://localhost:8000**

La app redirigirá a `Principal` por defecto. Sin Oracle configurado correctamente verás "Sistema fuera de línea" cuando se intente conectar a la base.

## Notas

- El **router** (`router.php`) hace el mismo papel que el `.htaccess` en Apache: todas las URLs se enrutan a `index.php?url=...`.
- Para producción se suele usar **Apache** o **Nginx** con el `DocumentRoot` en `backend/public` y las reglas de reescritura del `.htaccess`.
