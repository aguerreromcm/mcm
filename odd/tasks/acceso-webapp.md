# Acceso Webapp — user access admin panel for the payments webapp

Objective: add an "Acceso Webapp" option under Administración to manage webapp credentials (temporary password, unlock, unlink device, close sessions, access log) over `ESIACOM.API_EXT_*`, plus the temporary-password contract the webapp/API team must adopt.
Branch: `feat/acceso-webapp` · Delivery: ask-on-risk · Test runner: none in project (functional checks via PHP CLI against the test DB `mcm_pruebas`).

## Specs

- **S1 Source of truth.** The panel implements `panel-administracion.md` (user's Desktop) sections 3.1–3.7 and 4, except where S2 overrides. Key rules quoted verbatim:
  - "El panel nunca debe leer, copiar ni modificar `PE.CLAVE`."
  - "**Fechas en UTC.** ... Al escribir, use `SYS_EXTRACT_UTC(SYSTIMESTAMP)`. Al mostrar, convierta a la hora de México."
  - "**No se borran renglones** de `API_EXT_DISPOSITIVO` ni de `API_EXT_SESION`."
  - "**No escriba en `API_EXT_ACCESO`.**" / "**No toca `API_EXT_RETO`.**"
  - "**Nunca se muestra el hash** de la contraseña ni la llave pública completa."
- **S2 Temporary password (user, L2).** "Hay que implementar la contraseña temporal para que al primer ingreso se modifique. Dado que esto se debe controlar de este lado, tu indica lo necesario para que el equipo que desarrolla la webapp adapte lo necesario". Approved design:
  - `API_EXT_CREDENCIAL` gains `CAMBIO_REQUERIDO CHAR(1) DEFAULT 'N'` ('S'/'N') and `TEMPORAL_VENCE_UTC TIMESTAMP(3)`; existing rows stay 'N'.
  - The panel generates the temporary password (random, 12 chars, no ambiguous chars), shows it once, stores Argon2id (m=65536, t=3, p=4) with `CAMBIO_REQUERIDO='S'` and a 72-hour validity. Reset also sets intentos=0 and clears the lock.
  - `API_EXT_ACCESO.RESULTADO` admits `CAMBIO_CONTRASENA_REQUERIDO` and `CONTRASENA_TEMPORAL_VENCIDA`.
  - API/webapp contract (guide for the other team): no session nor device binding while `CAMBIO_REQUERIDO='S'`; single-use change token; change endpoint (min 10, ≠ temporary, ≠ usuario; UPDATE conditioned on the read hash); renewal fails when `CAMBIO_REQUERIDO='S'`; change-password screen.
- **S3 List (3.1).** Active `PE` users (`CDGEM='EMPFIN'`) with at least one admitted `UT.CDGTUS` (`OCOF`, `GTOCA`, `GPAGO`, `GREG`): usuario, nombre, perfil(es), sucursal; password state (sin contraseña / activa / temporal pendiente / temporal vencida / bloqueada hasta); phone bound since; last access (fecha, resultado, versión app).
- **S4 Actions.** Assign/reset temporary password (3.2 + S2; user must exist in `PE` with `CDGEM='EMPFIN'`), unlock (3.3), unlink phone (3.4, one transaction, warning text verbatim: *"Pide al ejecutivo que sincronice sus pagos pendientes antes de desligar el teléfono"* + explicit confirmation), close sessions (3.5).
- **S5 Access log (3.6).** `API_EXT_ACCESO` by usuario and date range, always bounded; shows attempt and device fields; IP/user agent only for admin role.
- **S6 Security (4).** Only profile `ADMIN`, enforced in the menu AND server-side in every endpoint. Every action is audited in `ESIACOM.API_EXT_ADMINISTRACION` (ID, FECHA_UTC, ADMINISTRADOR, USUARIO, ACCION, IP) inside the same transaction as the action. Uses the existing ESIACOM connection (deviation from §4 dedicated Oracle user — pending for production).
- **S7 Scripts.** DDL lives in `backend/database/` and is applied only to the test DB.

## Tasks

| ID | Specs | Route | Status | Commit |
|---|---|---|---|---|
| T1 | S2, S6, S7 | inline | done (applied to test DB) | d7cb7577 |
| T2 | S2 | inline | done | efbd3067 |
| T3 | S1, S3, S5 | inline | done (38/38 functional checks) | 0ae7a8cb |
| T4 | S1, S2, S4, S6 | inline | done (38/38 functional checks) | 0ae7a8cb |
| T5 | S3–S6 | inline | done (HTTP + browser checks) | 4ca57f0d |
| V1 | S1–S7 | verifier | done: 0 blockers, advisories fixed | see L6 |
| T6 | S2, S4 | inline | done (14/14 functional checks, see L8) | see L8 |

Route evidence: one coherent feature, units depend on each other (service ← controller ← view), no Writer trigger reason → inline. Risk: items 1 and 3 (existing table/constraint change consumed by the API) and 2 (credentials) → independent verifier at the end.

## Log

- **L1 (2026-11-19, user, verbatim):** "Se esta trabajando en una webapp para el registro de pago, esto busca cambiar el flujo habitual que es ir cobrar y volver a la sucursal para que una cajera registre el pago. Con la webapp ahora el ejecutivo va cobra y registra, al volver a la sucursal la cajera solo confirma. Lo que necesitamos de momento es un administrador de usuarios para la app, en el sistema que corre sobre flex se hace el registro y control de usuarios, pero este no tiene medidas de seguridad robustas, por lo que se implemento una contraseña independiente para proteger la que se usa en el sistema central. Te dejo los pormenores en el markdown adjunto, la idea es añadir una opción dentro de administración donde se pueda configurar lo necesario para que los usuarios puedan entrar y desligar el dispositivo que esta ligado al usuario."
- **L2 (2026-11-19, user, verbatim):** "Se que es hacer mas cambios, pero a futuro evitara dolores de cabeza. Hay que implementar la contraseña temporal para que al primer ingreso se modifique. Dado que esto se debe controlar de este lado, tu indica lo necesario para que el equipo que desarrolla la webapp adapte lo necesario"
- **L3 (2026-11-19):** User approved the panel proposal and the temporary-password design ("Sí, apruebo el diseño, adelante").
- **L5:** Checks: service functional script (38 PASS) against the test DB using user ABLF (no prior data) with a fake phone/session, cleaned up afterwards; HTTP: non-admin → 403 on every endpoint and redirect on the page, GET/form POST to actions → 405, no hash/key columns in responses; browser: assign flow, row refresh, filtered log, unlink warning text (cancelled). Test DB restored (14 credentials, 0 audit rows).
- **L6:** Independent verifier: S1–S7 PASS, 0 blockers. Fixed advisories: raw Oracle errors no longer reach the browser (`AccesoWebappService::fallo` logs them), non-string `usuario` rejected, reset dialog warns about session close, contract doc aligned to "≠ usuario" and clarifies the check goes before consuming the challenge, rollback added to the audit table script. Left as is: `Database::Conecta` exits with a 503 page that embeds the DB password when Oracle is down (pre-existing); "cerrar sesiones" also stamps expired sessions (matches the source doc SQL); audit IP is REMOTE_ADDR; default log dates use the browser clock.
- **L7:** Native review (4 lenses, committed range 45c6b6ea..ee930b4b): approved and acknowledged, 13 non-blocking findings. Main follow-ups: (a) reset does not revoke sessions server-side — today the 5-minute cutoff depends on the API change (risk, reliability and resilience agree); (b) nothing stops using the panel before the API adopts the contract (needs a flag or gate); (c) concurrent first assignment of the same user makes one admin get a generic error (PK violation, rolled back); (d) `panel-administracion.md` is not in the repo; (e) 72 h / 31 days repeated as literals in UI text; (f) no versioned tests (project has no runner).
- **L8 (2026-11-19, user, verbatim):** "Sí, hazlo aquí" — answering "¿Hago que restablecer la contraseña también cierre las sesiones desde el panel?". Follow-up (a) from L7: `asignarTemporal` now revokes open `API_EXT_SESION` rows in the same transaction as the MERGE and the audit row, and returns `sesiones_cerradas`; reset dialog and result text updated; contract doc §2, §3.4 and test 8 updated (the API renewal check stays as a second barrier). Checks: 14/14 functional checks against the test DB (assign with 0 sessions, reset revokes 2 open sessions, an already revoked session keeps its stamp, no rows deleted, phone stays linked, audit ASIGNAR→RESTABLECER only, cerrar/desligar/invalid user unchanged), cleanup verified (14 credentials, 0 audit rows); inline JS syntax checked with `node --check` (browser session expired, no UI click-through).
- **Pending (production):** dedicated Oracle user with minimal grants (§4); apply scripts in production only after the API/webapp adopt the contract; panel enabled last.
- **L4:** Evidence: test DB tables match the source doc; `API_EXT_DISP_VIGENTE_UX` enforces one active phone per user; PHP 8.2 `PASSWORD_ARGON2ID` yields `$argon2id$v=19$m=65536,t=3,p=4$...`. `.gitignore` ignores `*.sql`; existing scripts are force-added, so new scripts use `git add -f`.
