-- =============================================================================
-- SOLO PRUEBAS – Procedimiento de prueba para Operaciones → Conciliación de pagos.
-- Misma firma que spRedistribucionPagos; no modifica tablas.
-- Usar cuando CONCILIACION_SOLO_FLUJO = true en configuracion.ini.
-- =============================================================================

CREATE OR REPLACE PROCEDURE spRedistribucionPagos_PRUEBA (
  p_empresa       IN  VARCHAR2,
  p_cdgclns       IN  VARCHAR2,
  p_ciclo         IN  VARCHAR2,
  p_tipo          IN  VARCHAR2,
  p_fecha         IN  DATE,
  p_periodo       IN  NUMBER,
  p_secuencia     IN  VARCHAR2,
  p_monto         IN  NUMBER,
  p_cuenta        IN  VARCHAR2,
  p_usuario       IN  VARCHAR2,
  p_identificador IN  VARCHAR2
) IS
BEGIN
  -- SOLO PRUEBAS: no se modifica ninguna tabla.
  NULL;
END spRedistribucionPagos_PRUEBA;
