-- ============================================================================
-- BITACORA_CIERRE_DIARIO
-- Tabla de bitácora para el proceso de Cierre de Día (módulo Operaciones).
-- Necesaria para: control de concurrencia, últimos 5 cierres y tiempo estimado.
-- Ejecutar con el esquema que usa la aplicación (ej. ESIACOM).
-- ============================================================================

CREATE TABLE BITACORA_CIERRE_DIARIO (
    FECHA_CALCULO   DATE          NOT NULL,
    USUARIO         VARCHAR2(50) NOT NULL,
    INICIO          DATE          DEFAULT SYSDATE,
    FIN             DATE,
    EXITO           NUMBER(1)
);

COMMENT ON TABLE BITACORA_CIERRE_DIARIO IS 'Bitácora de ejecuciones del Cierre de Día. FIN IS NULL = proceso en ejecución.';
COMMENT ON COLUMN BITACORA_CIERRE_DIARIO.FECHA_CALCULO IS 'Fecha del cierre (día que se cerró)';
COMMENT ON COLUMN BITACORA_CIERRE_DIARIO.INICIO IS 'Momento de inicio del proceso (default SYSDATE al insertar)';
COMMENT ON COLUMN BITACORA_CIERRE_DIARIO.FIN IS 'Momento de fin; NULL mientras está en ejecución';
COMMENT ON COLUMN BITACORA_CIERRE_DIARIO.EXITO IS '1 = éxito, 0 = error';
