-- ============================================================================
-- API_EXT_CONTRASENA_TEMPORAL
-- Temporary password support for the payments webapp (panel "Acceso Webapp").
-- The admin panel assigns a temporary password; the API must force the user to
-- change it on first login (see docs/webapp/contrasena-temporal.md).
-- Run with the schema that owns the API_EXT_* tables (ESIACOM).
-- Requires: 004_identidad.sql and 005_auditoria_accesos.sql already applied.
-- ============================================================================

-- 1. Credential flags. Existing rows keep CAMBIO_REQUERIDO = 'N' (no forced change).
ALTER TABLE ESIACOM.API_EXT_CREDENCIAL ADD (
    CAMBIO_REQUERIDO    CHAR(1)      DEFAULT 'N' NOT NULL,
    TEMPORAL_VENCE_UTC  TIMESTAMP(3)
);

ALTER TABLE ESIACOM.API_EXT_CREDENCIAL ADD CONSTRAINT API_EXT_CRED_CAMBIO_CK
    CHECK (CAMBIO_REQUERIDO IN ('S', 'N'));

-- A pending temporary password always carries its expiry.
ALTER TABLE ESIACOM.API_EXT_CREDENCIAL ADD CONSTRAINT API_EXT_CRED_TEMP_CK
    CHECK (CAMBIO_REQUERIDO = 'N' OR TEMPORAL_VENCE_UTC IS NOT NULL);

COMMENT ON COLUMN ESIACOM.API_EXT_CREDENCIAL.CAMBIO_REQUERIDO IS 'S = current password is temporary and must be changed on next login; N = definitive password';
COMMENT ON COLUMN ESIACOM.API_EXT_CREDENCIAL.TEMPORAL_VENCE_UTC IS 'UTC expiry of the temporary password; NULL when CAMBIO_REQUERIDO = N';

-- 2. New login results written by the API to the access log.
ALTER TABLE ESIACOM.API_EXT_ACCESO DROP CONSTRAINT API_EXT_ACC_RES_CK;

ALTER TABLE ESIACOM.API_EXT_ACCESO ADD CONSTRAINT API_EXT_ACC_RES_CK
    CHECK (RESULTADO IN (
        'CORRECTO', 'CREDENCIALES_INVALIDAS', 'CUENTA_BLOQUEADA',
        'DISPOSITIVO_NO_AUTORIZADO', 'RETO_INVALIDO', 'NO_DISPONIBLE', 'ERROR_INTERNO',
        'CAMBIO_CONTRASENA_REQUERIDO', 'CONTRASENA_TEMPORAL_VENCIDA'
    ));

-- ----------------------------------------------------------------------------
-- Rollback (only if no row uses the new values):
-- ALTER TABLE ESIACOM.API_EXT_ACCESO DROP CONSTRAINT API_EXT_ACC_RES_CK;
-- ALTER TABLE ESIACOM.API_EXT_ACCESO ADD CONSTRAINT API_EXT_ACC_RES_CK CHECK (RESULTADO IN (
--     'CORRECTO', 'CREDENCIALES_INVALIDAS', 'CUENTA_BLOQUEADA',
--     'DISPOSITIVO_NO_AUTORIZADO', 'RETO_INVALIDO', 'NO_DISPONIBLE', 'ERROR_INTERNO'));
-- ALTER TABLE ESIACOM.API_EXT_CREDENCIAL DROP CONSTRAINT API_EXT_CRED_TEMP_CK;
-- ALTER TABLE ESIACOM.API_EXT_CREDENCIAL DROP CONSTRAINT API_EXT_CRED_CAMBIO_CK;
-- ALTER TABLE ESIACOM.API_EXT_CREDENCIAL DROP (CAMBIO_REQUERIDO, TEMPORAL_VENCE_UTC);
-- ----------------------------------------------------------------------------
