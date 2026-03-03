-- =============================================================================
-- Aplicar Pagos por pago: marca en PAGOSDIA cuándo se importó cada registro.
-- La app actualiza F_IMPORTACION tras cada llamada exitosa al SP de importación.
-- NULL = pendiente de aplicar; con valor = ya aplicado.
-- Ejecutar este script en Oracle antes de usar "Aplicar pagos" por pago.
-- =============================================================================

ALTER TABLE PAGOSDIA ADD F_IMPORTACION TIMESTAMP;

COMMENT ON COLUMN PAGOSDIA.F_IMPORTACION IS 'Timestamp de importación (Aplicar Pagos). NULL = pendiente.';

-- Permitir varias ejecuciones por fecha (cada ejecución aplica solo pendientes).
-- Si el nombre de la restricción es otro, ajustar o consultar: SELECT CONSTRAINT_NAME FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'PAGOS_PROCESADOS' AND CONSTRAINT_TYPE = 'U';
ALTER TABLE PAGOS_PROCESADOS DROP CONSTRAINT UK_PAGOS_PROCESADOS_FECHA;
