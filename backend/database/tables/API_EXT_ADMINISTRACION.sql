-- ============================================================================
-- API_EXT_ADMINISTRACION
-- Audit trail of the "Acceso Webapp" admin panel (Administración menu).
-- One row per admin action, written in the same transaction as the action.
-- Rows are never updated nor deleted.
-- Run with the schema that owns the API_EXT_* tables (ESIACOM).
-- ============================================================================

CREATE TABLE ESIACOM.API_EXT_ADMINISTRACION (
    ID              VARCHAR2(32)  DEFAULT LOWER(RAWTOHEX(SYS_GUID())) NOT NULL,
    FECHA_UTC       TIMESTAMP(3)  DEFAULT SYS_EXTRACT_UTC(SYSTIMESTAMP) NOT NULL,
    ADMINISTRADOR   VARCHAR2(6)   NOT NULL,
    USUARIO         VARCHAR2(6)   NOT NULL,
    ACCION          VARCHAR2(30)  NOT NULL,
    IP              VARCHAR2(45),
    CONSTRAINT API_EXT_ADM_PK PRIMARY KEY (ID),
    CONSTRAINT API_EXT_ADM_ID_CK CHECK (REGEXP_LIKE(ID, '^[0-9a-f]{32}$')),
    CONSTRAINT API_EXT_ADM_ACC_CK CHECK (ACCION IN (
        'ASIGNAR_CONTRASENA', 'RESTABLECER_CONTRASENA', 'DESBLOQUEAR',
        'DESLIGAR_DISPOSITIVO', 'CERRAR_SESIONES'
    ))
);

CREATE INDEX ESIACOM.API_EXT_ADM_USUARIO_IX ON ESIACOM.API_EXT_ADMINISTRACION (USUARIO, FECHA_UTC);
CREATE INDEX ESIACOM.API_EXT_ADM_FECHA_IX ON ESIACOM.API_EXT_ADMINISTRACION (FECHA_UTC);

COMMENT ON TABLE ESIACOM.API_EXT_ADMINISTRACION IS 'Audit trail of the Acceso Webapp admin panel: who did what to which user and when';
COMMENT ON COLUMN ESIACOM.API_EXT_ADMINISTRACION.FECHA_UTC IS 'UTC timestamp of the action';
COMMENT ON COLUMN ESIACOM.API_EXT_ADMINISTRACION.ADMINISTRADOR IS 'PE.CODIGO of the admin who performed the action';
COMMENT ON COLUMN ESIACOM.API_EXT_ADMINISTRACION.USUARIO IS 'PE.CODIGO of the affected webapp user';
COMMENT ON COLUMN ESIACOM.API_EXT_ADMINISTRACION.ACCION IS 'Action performed by the admin';
COMMENT ON COLUMN ESIACOM.API_EXT_ADMINISTRACION.IP IS 'IP address the admin used';

-- Production with a dedicated panel user (pending decision):
-- GRANT SELECT, INSERT ON ESIACOM.API_EXT_ADMINISTRACION TO <USUARIO_PANEL>;
