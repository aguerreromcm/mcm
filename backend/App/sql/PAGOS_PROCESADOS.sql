-- Tabla de control para aplicación de pagos (Operaciones -> Aplicar Pagos).
-- Evita reprocesar la misma fecha y guarda resumen por ejecución.

CREATE TABLE PAGOS_PROCESADOS (
    ID                  NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    FECHA_PROCESO       DATE NOT NULL,
    TOTAL_REGISTROS     NUMBER(12) NOT NULL,
    TOTAL_IMPORTE       NUMBER(18,2) NOT NULL,
    USUARIO             VARCHAR2(30) NOT NULL,
    FECHA_EJECUCION     TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    ESTADO              VARCHAR2(20) NOT NULL,
    MENSAJE             VARCHAR2(4000),
    DETALLE_JSON        CLOB,
    CONSTRAINT UK_PAGOS_PROCESADOS_FECHA UNIQUE (FECHA_PROCESO)
);

COMMENT ON TABLE PAGOS_PROCESADOS IS 'Control de fechas ya procesadas en Aplicar Pagos (Layout contable)';
COMMENT ON COLUMN PAGOS_PROCESADOS.FECHA_PROCESO IS 'Fecha de pago procesada (única por día)';
COMMENT ON COLUMN PAGOS_PROCESADOS.ESTADO IS 'OK, ERROR';
COMMENT ON COLUMN PAGOS_PROCESADOS.DETALLE_JSON IS 'Detalle de registros aplicados o error para auditoría';
