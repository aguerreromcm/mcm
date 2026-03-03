-- =============================================================================
-- SOLO PRUEBAS – Package de prueba para Operaciones → Aplicar Pagos.
-- Misma firma que PKG_ImportaPagoSOF.spImportaPagoSOF; no modifica tablas.
-- Eliminar o desactivar cuando la base de pruebas/producción esté lista.
-- =============================================================================

CREATE OR REPLACE PACKAGE PKG_ImportaPagoSOF_PRUEBA AS
  PROCEDURE spImportaPagoSOF(
    p_fecha     IN  VARCHAR2,
    p_ref       IN  VARCHAR2,
    p_monto     IN  NUMBER,
    p_empresa   IN  VARCHAR2,
    p_cta       IN  VARCHAR2,
    p_user      IN  VARCHAR2,
    p_iden      IN  VARCHAR2,
    p_periodo   IN  NUMBER,
    p_oper      IN  VARCHAR2,
    p_res       IN OUT VARCHAR2,
    p_montocan  IN  NUMBER,
    p_renexcel  IN  NUMBER,
    p_renglon   IN  NUMBER,
    p_nopagos   IN  NUMBER,
    p_idimp     IN  NUMBER,
    p_val       IN OUT NUMBER,
    p_moneda    IN  VARCHAR2
  );
END PKG_ImportaPagoSOF_PRUEBA;

-- Ejecutar en SQL Developer: primero este bloque (Package), luego el siguiente (Package Body).
-- En SQL*Plus puedes poner "/" después de cada END si lo usas por línea de comandos.

CREATE OR REPLACE PACKAGE BODY PKG_ImportaPagoSOF_PRUEBA AS
  PROCEDURE spImportaPagoSOF(
    p_fecha     IN  VARCHAR2,
    p_ref       IN  VARCHAR2,
    p_monto     IN  NUMBER,
    p_empresa   IN  VARCHAR2,
    p_cta       IN  VARCHAR2,
    p_user      IN  VARCHAR2,
    p_iden      IN  VARCHAR2,
    p_periodo   IN  NUMBER,
    p_oper      IN  VARCHAR2,
    p_res       IN OUT VARCHAR2,
    p_montocan  IN  NUMBER,
    p_renexcel  IN  NUMBER,
    p_renglon   IN  NUMBER,
    p_nopagos   IN  NUMBER,
    p_idimp     IN  NUMBER,
    p_val       IN OUT NUMBER,
    p_moneda    IN  VARCHAR2
  ) IS
  BEGIN
    -- SOLO PRUEBAS: no se modifica ninguna tabla.
    p_res := 'OK PRUEBA (sin cambios en BD)';
    p_val := 1;
  END spImportaPagoSOF;
END PKG_ImportaPagoSOF_PRUEBA;
