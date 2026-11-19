# Contraseña temporal en la webapp de pagos

Guía para el equipo de la API y la webapp. Describe qué deben cambiar para que la contraseña que asigna el panel de administración sea **temporal** y el usuario la cambie en su primer inicio de sesión.

Complementa `panel-administracion.md`. Donde las dos guías difieran, manda esta.

## 1. Qué cambia y por qué

Antes, el administrador escribía la contraseña del usuario y la conocía. Ahora:

1. El panel **genera** una contraseña temporal aleatoria de 12 caracteres y la muestra una sola vez, para entregarla en persona.
2. La contraseña temporal vale **72 horas**.
3. En su primer inicio de sesión, el usuario debe **cambiarla** por una que solo él conoce. Mientras no la cambie, no obtiene sesión ni liga su teléfono.

La regla vive en la base de datos y la controla el panel. La API solo debe respetarla.

## 2. Cambios en la base (ya aplicados en pruebas)

Script: `backend/database/scripts/API_EXT_CONTRASENA_TEMPORAL.sql`.

### `ESIACOM.API_EXT_CREDENCIAL`: dos columnas nuevas

| Columna | Tipo | Significado |
|---|---|---|
| `CAMBIO_REQUERIDO` | `CHAR(1)`, `NOT NULL`, por omisión `'N'` | `'S'`: la contraseña vigente es temporal y debe cambiarse. `'N'`: es definitiva. |
| `TEMPORAL_VENCE_UTC` | `TIMESTAMP(3)` | Cuándo vence la temporal, en UTC. Es nula cuando `CAMBIO_REQUERIDO = 'N'`. |

Restricciones nuevas:

- `API_EXT_CRED_CAMBIO_CK`: `CAMBIO_REQUERIDO IN ('S', 'N')`.
- `API_EXT_CRED_TEMP_CK`: si `CAMBIO_REQUERIDO = 'S'`, `TEMPORAL_VENCE_UTC` no puede ser nula.

Los usuarios que ya existían quedaron con `'N'`, así que no se les obliga a cambiar nada.

### `ESIACOM.API_EXT_ACCESO`: dos resultados nuevos

La restricción `API_EXT_ACC_RES_CK` ahora admite también:

| Valor | Cuándo lo escribe la API |
|---|---|
| `CAMBIO_CONTRASENA_REQUERIDO` | La contraseña fue correcta, pero es temporal y está vigente. No se creó sesión. |
| `CONTRASENA_TEMPORAL_VENCIDA` | La contraseña fue correcta, pero es temporal y ya venció. |

### Lo que escribe el panel al asignar o restablecer

```sql
MERGE INTO ESIACOM.API_EXT_CREDENCIAL C
USING (SELECT :usuario AS USUARIO FROM DUAL) N
ON (C.USUARIO = N.USUARIO)
WHEN MATCHED THEN UPDATE SET
    C.HASH_CONTRASENA = :hash, C.INTENTOS_FALLIDOS = 0, C.BLOQUEADO_HASTA_UTC = NULL,
    C.CAMBIO_REQUERIDO = 'S',
    C.TEMPORAL_VENCE_UTC = SYS_EXTRACT_UTC(SYSTIMESTAMP) + INTERVAL '72' HOUR,
    C.ACTUALIZADO_UTC = SYS_EXTRACT_UTC(SYSTIMESTAMP)
WHEN NOT MATCHED THEN INSERT (...) VALUES (..., 'S', SYS_EXTRACT_UTC(SYSTIMESTAMP) + INTERVAL '72' HOUR, ...);
```

El hash es Argon2id en formato PHC (`$argon2id$v=19$m=65536,t=3,p=4$...`), igual que el que ya genera la API.

## 3. Cambios en la API

### 3.1 Inicio de sesión

Las validaciones de hoy se quedan como están y en el mismo orden: el usuario existe y está activo en SICAFIN, tiene un perfil admitido, no está bloqueado por intentos, la contraseña es correcta y el teléfono está autorizado.

Si un usuario ya tiene un teléfono ligado y entra desde otro, sigue respondiendo `DISPOSITIVO_NO_AUTORIZADO`, aunque su contraseña sea temporal.

**Después de comprobar que la contraseña es correcta** y antes de crear la sesión o ligar el teléfono, agregue esta revisión.

Si la API valida el teléfono consumiendo el reto, la revisión va **después** de rechazar un teléfono distinto al ligado, pero **antes** de consumir el reto. Así, el reto sigue disponible para el inicio de sesión que la webapp hace tras el cambio.

| Estado de la credencial | Qué hace la API |
|---|---|
| `CAMBIO_REQUERIDO = 'N'` | Sigue el flujo normal, sin cambios. |
| `CAMBIO_REQUERIDO = 'S'` y `TEMPORAL_VENCE_UTC > ahora` | Pone los intentos fallidos en 0. **No** crea sesión, **no** liga el teléfono y **no** consume el reto. Escribe `CAMBIO_CONTRASENA_REQUERIDO` en la bitácora y responde con un **token de cambio** (punto 3.2). |
| `CAMBIO_REQUERIDO = 'S'` y `TEMPORAL_VENCE_UTC <= ahora` | Rechaza la entrada sin sumar un intento fallido, porque la contraseña fue correcta. Escribe `CONTRASENA_TEMPORAL_VENCIDA` en la bitácora. |

Respuesta sugerida (la forma exacta la decide el equipo de la API, pero debe distinguir los tres casos):

```json
// HTTP 403: cambio requerido
{ "codigo": "CAMBIO_CONTRASENA_REQUERIDO", "token_cambio": "<token>", "vence_en_segundos": 600 }

// HTTP 401: temporal vencida
{ "codigo": "CONTRASENA_TEMPORAL_VENCIDA" }
```

Revelar que la temporal venció no filtra información, porque solo se responde cuando la contraseña fue correcta.

### 3.2 Token de cambio

El token de cambio solo sirve para cambiar la contraseña:

- **Contenido:** el usuario y una huella del hash vigente (por ejemplo, los primeros 16 caracteres del SHA-256 de `HASH_CONTRASENA`). Va firmado con la llave del servidor o se guarda del lado del servidor. **Nunca** incluya la contraseña ni el hash completo.
- **Duración:** 10 minutos como máximo.
- **Un solo uso.** Se cumple solo: en cuanto el cambio se aplica, el hash cambia y la huella ya no coincide. Por eso no hace falta una tabla nueva.
- No sirve como token de acceso ni para renovar la sesión.

### 3.3 Endpoint nuevo: cambiar la contraseña

Por ejemplo, `POST /auth/cambiar-contrasena`, con `token_cambio` y `nueva_contrasena` en el cuerpo. Pasos:

1. Valide la firma y la vigencia del token.
2. Vuelva a validar el usuario en SICAFIN (activo, sin bloqueo y con perfil admitido), igual que en el inicio de sesión.
3. Lea la credencial. Debe tener `CAMBIO_REQUERIDO = 'S'`, `TEMPORAL_VENCE_UTC` mayor que ahora y una huella que coincida con la del token. Si algo no cumple, responda "token inválido o vencido".
4. Valide la nueva contraseña:
   - tiene **al menos 10 caracteres**;
   - **no es igual a la temporal** (verifíquela contra el hash vigente con Argon2id);
   - **no es igual al usuario**, sin distinguir mayúsculas.
5. Genere el hash Argon2id con al menos `m=65536` y `t=3`, y guárdelo con un `UPDATE` **condicionado al hash que leyó**:

   ```sql
   UPDATE ESIACOM.API_EXT_CREDENCIAL
   SET HASH_CONTRASENA = :hash_nuevo,
       CAMBIO_REQUERIDO = 'N',
       TEMPORAL_VENCE_UTC = NULL,
       INTENTOS_FALLIDOS = 0,
       BLOQUEADO_HASTA_UTC = NULL,
       ACTUALIZADO_UTC = SYS_EXTRACT_UTC(SYSTIMESTAMP)
   WHERE USUARIO = :usuario
     AND CAMBIO_REQUERIDO = 'S'
     AND HASH_CONTRASENA = :hash_leido;
   ```

   Si actualiza 0 renglones, el administrador restableció la contraseña a mitad del proceso o el token ya se usó. Responda "token inválido o vencido" y no reintente.
6. Responda que el cambio fue correcto. **No** cree sesión aquí: la webapp hace a continuación un inicio de sesión normal con la contraseña nueva. Así, el reto, el ligado del teléfono y la creación de la sesión siguen exactamente como hoy.

Nunca registre en logs la contraseña temporal, la nueva ni el token.

### 3.4 Renovación (cada 5 minutos)

En la misma consulta en la que hoy se revisa SICAFIN, lea también `CAMBIO_REQUERIDO`. Si vale `'S'`:

- rechace la renovación;
- revoque la sesión (`REVOCADA_UTC = SYS_EXTRACT_UTC(SYSTIMESTAMP)`).

Así, cuando el administrador restablece una contraseña, el usuario sale de la webapp en **5 minutos como máximo**. El teléfono **no** se desliga: el usuario vuelve a entrar desde el mismo equipo con la temporal, la cambia, y sus pagos pendientes siguen pudiendo sincronizarse.

### 3.5 Comando de consola

`python -m api_externa.credenciales asignar` queda reemplazado por el panel. Mientras siga existiendo, su `MERGE` debe escribir de forma explícita `CAMBIO_REQUERIDO = 'N'` y `TEMPORAL_VENCE_UTC = NULL`. Si no lo hace, un usuario con una temporal pendiente seguiría obligado a cambiarla aunque se le asigne una contraseña nueva por consola.

## 4. Cambios en la webapp

1. **Pantalla "Cambia tu contraseña".** Aparece cuando el inicio de sesión responde `CAMBIO_CONTRASENA_REQUERIDO`.
   - Campos: nueva contraseña y confirmación, ambos ocultos y con opción de mostrar.
   - Muestre las reglas antes de enviar: al menos 10 caracteres, distinta de la temporal y distinta del usuario. Valídelas también del lado del cliente, sin sustituir las de la API.
   - Si el token vence (10 minutos), regrese al inicio de sesión con el mensaje *"Tu tiempo para cambiar la contraseña terminó. Vuelve a iniciar sesión con tu contraseña temporal."*
2. **Después del cambio,** inicie sesión automáticamente con la contraseña nueva usando el flujo normal (reto y teléfono), sin pedirla otra vez.
3. **Temporal vencida.** Muestre *"Tu contraseña temporal venció. Pide a tu administrador que te asigne una nueva."*
4. **Sesión cerrada por restablecimiento.** Cuando la renovación falla, la webapp ya regresa al inicio de sesión. No hace falta un mensaje especial.
5. **Sin conexión.** El cambio de contraseña requiere conexión. Los pagos guardados en el teléfono no se tocan.

## 5. Compatibilidad y orden de despliegue

- **La base es compatible con la API actual.** Las columnas nuevas tienen valor por omisión y la API de hoy no las lee.
- **Hasta que la API se adapte,** una contraseña temporal funciona como una contraseña normal: el usuario entra, pero no se le obliga a cambiarla. Por eso, en producción, **el panel no debe habilitarse antes que la API nueva**.
- Orden en producción: (1) scripts de base, (2) API y webapp, (3) panel.

## 6. Pruebas de aceptación

| # | Caso | Resultado esperado |
|---|---|---|
| 1 | Entrar con una temporal vigente | Responde `CAMBIO_CONTRASENA_REQUERIDO` con token; no hay renglón nuevo en `API_EXT_SESION` ni en `API_EXT_DISPOSITIVO`; la bitácora registra `CAMBIO_CONTRASENA_REQUERIDO`. |
| 2 | Cambiar con una contraseña válida | `CAMBIO_REQUERIDO = 'N'` y `TEMPORAL_VENCE_UTC` nula; el inicio de sesión automático liga el teléfono y crea la sesión. |
| 3 | Reusar el mismo token de cambio | "Token inválido o vencido". |
| 4 | Nueva contraseña de 9 caracteres, igual a la temporal o igual al usuario | Se rechaza y la credencial no cambia. |
| 5 | El administrador restablece entre la emisión del token y el cambio | El `UPDATE` afecta 0 renglones y se responde "token inválido o vencido". |
| 6 | Entrar con una temporal vencida | Responde `CONTRASENA_TEMPORAL_VENCIDA`; `INTENTOS_FALLIDOS` no aumenta. |
| 7 | Contraseña temporal incorrecta 5 veces | Se bloquea 15 minutos, como hoy. |
| 8 | Restablecer con una sesión abierta | Antes de 5 minutos la renovación falla y la sesión queda con `REVOCADA_UTC`; el teléfono sigue ligado. |
| 9 | Usuario con teléfono ligado entra con la temporal desde otro teléfono | `DISPOSITIVO_NO_AUTORIZADO`. |
| 10 | Usuario dado de baja en SICAFIN con una temporal vigente | `CREDENCIALES_INVALIDAS`, como hoy. |
