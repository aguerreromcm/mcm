<?php

namespace Core;

include_once dirname(__DIR__) . "/Core/App.php";

use PDO;

/**
 * @class Conn
 */

class Database
{
    private $configuracion;
    public $db_activa;

    function __construct($s = null, $u = null, $p = null)
    {
        $this->configuracion = App::getConfig();
        $this->Conecta($s, $u, $p);
    }

    private function Conecta($s = null, $u = null, $p = null)
    {
        $s = $this->configuracion[$s] ?? $s;
        $servidor = $s ?? $this->configuracion['SERVIDOR'];
        $esquema = $this->configuracion['ESQUEMA'] ?? 'ESIACOM';

        $cadena = "oci:dbname=//$servidor:1521/$esquema;charset=UTF8";
        $usuario = $u ?? $this->configuracion['USUARIO'];
        $password = $p ?? $this->configuracion['PASSWORD'];
        try {
            $this->db_activa =  new PDO($cadena, $usuario, $password);
        } catch (\PDOException $e) {
            self::baseNoDisponible("{$e->getMessage()}\nDatos de conexión: $cadena\nUsuario: $usuario\nPassword: $password");
            $this->db_activa =  null;
        }
    }

    private function baseNoDisponible($mensaje)
    {
        http_response_code(503);
        echo <<<HTML
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Sistema fuera de línea</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        text-align: center;
                        background-color: #f4f4f4;
                        color: #333;
                        margin: 0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                    }
                    .container {
                        background-color: #fff;
                        padding: 20px;
                        border-radius: 10px;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    }
                    h1 {
                        font-size: 2em;
                        color: #d9534f;
                    }
                    p {
                        font-size: 1.2em;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Sistema fuera de línea</h1>
                    <p>Estamos trabajando para resolver la situación. Por favor, vuelva a intentarlo más tarde.</p>
                </div>
                <input type="hidden" id="baseNoDisponible" value="$mensaje">
            </body>
            <script>
                window.onload = () => {
                    console.log(document.getElementById('baseNoDisponible').value)
                }
            </script>
            </html>
        HTML;
        exit();
    }

    public function AutoCommitOff()
    {
        $this->db_activa->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    }

    public function AutoCommitOn()
    {
        $this->db_activa->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }

    public function IniciaTransaccion()
    {
        $this->db_activa->beginTransaction();
    }

    public function CancelaTransaccion()
    {
        $this->db_activa->rollBack();
    }

    public function ConfirmaTransaccion()
    {
        $this->db_activa->commit();
    }

    private function muestraError($e, $sql = null, $parametros = null)
    {
        $error = "Error en DB: " . $e->getMessage();

        if ($sql != null) $error .= "\nSql: " . $sql;
        if ($parametros != null) $error .= "\nDatos: " . print_r($parametros, 1);
        echo $error . "\n";
        return $error;
    }

    public function insert($sql)
    {
        $stmt = $this->db_activa->prepare($sql);
        $result = $stmt->execute();

        if ($result) {
            echo '1';
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    public function insert_bene($sql)
    {
        $stmt = $this->db_activa->prepare($sql);
        $result = $stmt->execute();

        if ($result) {
            //echo '1';
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    public function insertar($sql, $datos)
    {
        try {
            if (!$this->db_activa->prepare($sql)->execute($datos))
                throw new \Exception("Error en insertar: " . print_r($this->db_activa->errorInfo(), 1) . "\nSql : $sql \nDatos : " . print_r($datos, 1));
        } catch (\PDOException $e) {
            throw new \Exception("Error en insertar: " . $e->getMessage() . "\nSql : $sql \nDatos : " . print_r($datos, 1));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function insertarBlob($sql, $datos, $blob = [])
    {
        try {
            $this->db_activa->beginTransaction();
            $stmt = $this->db_activa->prepare($sql);

            foreach ($datos as $key => $value) {
                if (in_array($key, $blob)) $stmt->bindValue(":$key", $value, PDO::PARAM_LOB);
                else $stmt->bindValue(":$key", $value);
            }

            if (!$stmt->execute()) throw new \Exception("Error en insertarBlob: " . print_r($this->db_activa->errorInfo(), 1) . "\nSql : $sql \nDatos : " . print_r($datos, 1));
            $this->db_activa->commit();
        } catch (\PDOException $e) {
            throw new \Exception("Error en insertarBlob: " . $e->getMessage() . "\nSql : $sql \nDatos : " . print_r($datos, 1));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function insertCheques($sql, $parametros)
    {
        $stmt = $this->db_activa->prepare($sql);
        $result = $stmt->execute($parametros);

        if ($result) return $result;

        $arr = $stmt->errorInfo();
        return "PDOStatement::errorInfo():\n" . json_encode($arr);
    }

    public function insertaMultiple($sql, $registros, $validacion = null, $res = false)
    {
        try {
            $resultados = [];
            $this->db_activa->beginTransaction();
            foreach ($registros as $i => $valores) {
                $stmt = $this->db_activa->prepare($sql[$i]);
                $result = $stmt->execute($valores);
                $resultados[] = $result;
                if (!$result) {
                    $err = $stmt->errorInfo();
                    $this->db_activa->rollBack();
                    throw new \Exception("Error en insertaMultiple: " . print_r($err, 1) . "\nSql: " . $sql[$i] . "\nDatos: " . print_r($valores, 1));
                }
            }

            if ($validacion != null) {
                $stmt = $this->db_activa->prepare($validacion['query']);
                $stmt->execute($validacion['datos']);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $resValidacion = $validacion['funcion']($result);
                if ($resValidacion['success'] == false) {
                    $this->db_activa->rollBack();
                    throw new \Exception($resValidacion['mensaje']);
                }
            }

            $this->db_activa->commit();
            return !$res ? true : $resultados;
        } catch (\PDOException $e) {
            $this->db_activa->rollBack();
            throw new \Exception("Error en insertaMultiple: " . $e->getMessage() . "\nSql: " . $sql[$i] . "\nDatos: " . print_r($valores, 1));
        } catch (\Exception $e) {
            $this->db_activa->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function EjecutaSP($sp, $parametros)
    {
        try {
            $stmt = $this->db_activa->prepare($sp);

            foreach ($parametros as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $output = '';
            $stmt->bindParam(':output', $output, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 4000);
            $stmt->execute();

            return $output;
        } catch (\PDOException $e) {
            throw new \Exception("Error en EjecutaSP: " . $e->getMessage() . "\nSP: $sp \nDatos: " . print_r($parametros, 1));
        }
    }

    public function EjecutaSP_DBMS_OUTPUT($sp, $parametros)
    {
        try {
            $stmt = $this->db_activa->prepare("BEGIN DBMS_OUTPUT.ENABLE(NULL); END;");
            $stmt->execute();
            $stmt = $this->db_activa->prepare($sp);

            foreach ($parametros as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();

            $stmt = $this->db_activa->prepare("SELECT COLUMN_VALUE AS RESULTADO FROM TABLE(GET_DBMS_OUTPUT)");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error en EjecutaSP_DBMS_OUTPUT: " . $e->getMessage() . "\nSP: $sp \nDatos: " . print_r($parametros, 1));
        }
    }

    public function eliminar($sql, $prm = null)
    {
        try {
            return $this->db_activa->prepare($sql)->execute($prm);
        } catch (\PDOException $e) {
            throw new \Exception("Error en eliminar: " . $e->getMessage() . "\nSql : $sql");
        }
    }

    public function actualizar($sql, $datos)
    {
        try {
            $resultado = $this->db_activa->prepare($sql);
            $resultado->execute($datos);
            if ($resultado->rowCount() == 0) return false;
            return true;
        } catch (\PDOException $e) {
            throw new \Exception("Error en actualizar: " . $e->getMessage() . "\nSql : $sql \nDatos : " . print_r($datos, 1));
        }
    }

    public function queryOne($sql, $params = null)
    {
        if ($params == null) {
            try {
                $stmt = $this->db_activa->query($sql);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array_shift($res);
            } catch (\PDOException $e) {
                self::muestraError($e, $sql, $params);
                return false;
            }
        } else {
            try {
                $stmt = $this->db_activa->prepare($sql);
                foreach ($params as $values => $val) {
                    $stmt->bindParam($values, $val);
                }

                $stmt->execute($params);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array_shift($res);
            } catch (\PDOException $e) {
                self::muestraError($e, $sql, $params);
                return false;
            }
        }
    }

    public function queryAll($sql, $params = null)
    {
        if ($params == null) {
            try {
                $stmt = $this->db_activa->query($sql);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                self::muestraError($e, $sql, $params);
                return false;
            }
        } else {
            try {
                $stmt = $this->db_activa->prepare($sql);
                foreach ($params as $values => $val) {
                    $stmt->bindParam($values, $val);
                }
                $stmt->execute($params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                self::muestraError($e, $sql, $params);
                return false;
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////7
    public function queryValidacionClienteCO($cdgns_, $ciclo_)
    {
        $empresa = "EMPFIN";
        $cdgns = $cdgns_;
        $ciclo = $ciclo_;
        $clns = "G";
        $fecha = date("d-m-Y");

        // ACTIVAR DBMS_OUTPUT
        $this->db_activa->exec("BEGIN DBMS_OUTPUT.ENABLE(NULL); END;");

        // LLAMADA AL SP
        $query_text = "BEGIN ESIACOM.SP_VALIDACION_CLIENTE_CO(:p1,:p2,:p3,:p4,:p5); END;";
        $stmt = $this->db_activa->prepare($query_text);

        $stmt->bindParam(':p1', $empresa);
        $stmt->bindParam(':p2', $cdgns);
        $stmt->bindParam(':p3', $ciclo);
        $stmt->bindParam(':p4', $clns);
        $stmt->bindParam(':p5', $fecha);

        $stmt->execute();

        // LEER SALIDA DEL DBMS_OUTPUT
        $output = "";
        $readStmt = $this->db_activa->prepare("
        DECLARE 
            l_line VARCHAR2(32000);
            l_done NUMBER;
        BEGIN
            LOOP
                DBMS_OUTPUT.GET_LINE(l_line, l_done);
                EXIT WHEN l_done = 1;
                :text := :text || l_line || CHR(10);
            END LOOP;
        END;
    ");

        $readStmt->bindParam(':text', $output, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 40000);
        $readStmt->execute();

        $resultado = ltrim($output);
        return $resultado; // ← AQUÍ REGRESA TODO EL TEXTO REAL
    }


    public function queryProcedurePago($credito, $ciclo_, $monto_, $tipo_, $nombre_, $user_, $ejecutivo_id,  $ejec_nom_, $tipo_procedure, $fecha_aux, $secuencia, $fecha)
    {

        $newDate = date("d-m-Y", strtotime($fecha));
        $newDateFechaAux = date("d-m-Y", strtotime($fecha_aux));

        $empresa = "EMPFIN";
        $fecha = $newDate;
        $fecha_aux =  $newDateFechaAux;
        $cdgns = $credito;
        $ciclo = $ciclo_;
        $secuencia = $secuencia;
        $nombre = $nombre_;
        $cdgocpe = $ejecutivo_id;
        $ejecutivo = $ejec_nom_;
        $cdgpe = $user_;
        $monto = $monto_;
        $tipo_mov = $tipo_;
        $tipo = $tipo_procedure;
        $resultado = "";
        $identifica_app = "";

        $query_text = "CALL SPACCIONPAGODIA_PRUEBA(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        ///$query_text = "CALL SPACCIONPAGODIA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";/////este es el que funciona bien cuando se actualice la base de datos de produccion
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $fecha, PDO::PARAM_STR);
        $stmt->bindParam(3, $fecha_aux, PDO::PARAM_STR);
        $stmt->bindParam(4, $cdgns, PDO::PARAM_STR);
        $stmt->bindParam(5, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(6, $secuencia, PDO::PARAM_STR);
        $stmt->bindParam(7, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(8, $cdgocpe, PDO::PARAM_STR);
        $stmt->bindParam(9, $ejecutivo, PDO::PARAM_STR);
        $stmt->bindParam(10, $cdgpe, PDO::PARAM_STR);
        $stmt->bindParam(11, $monto, PDO::PARAM_STR);
        $stmt->bindParam(12, $tipo_mov, PDO::PARAM_STR);
        $stmt->bindParam(13, $tipo, PDO::PARAM_INT, 10);
        $stmt->bindParam(14, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);
        //$stmt->bindParam(15,$identifica_app, PDO::PARAM_STR);


        $result = $stmt->execute();

        if ($result) {
            echo $resultado;
            return $resultado;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    /**
     * Ejecuta PKG_ImportaPagoSOF.spImportaPagoSOF (mismo SP que usa VB6 para importación de pagos).
     * No modificar firma ni lógica; solo invoca el procedimiento existente.
     *
     * @param string $fechaPago Fecha de pago (Y-m-d H:i:s)
     * @param string $referencia Referencia del pago
     * @param string $monto Monto numérico
     * @param string $empresa Empresa (ej. EMPFIN)
     * @param string $cuentaBancaria Código cuenta 2 caracteres
     * @param string $usuario Usuario que ejecuta
     * @param string $identificador Identificador de lote
     * @param int $renExcel Renglon en Excel
     * @param int $renglon Índice en lote
     * @param int $noPagos Total pagos en lote
     * @param int $idImportacion Id importación
     * @param string $moneda Moneda (MN)
     * @return array ['success' => bool, 'resultado' => string, 'validacion' => int]
     */
    public function spImportaPagoSOF($fechaPago, $referencia, $monto, $empresa, $cuentaBancaria, $usuario, $identificador, $renExcel, $renglon, $noPagos, $idImportacion, $moneda = 'MN')
    {
        $periodo = '1';
        $operacion = 'I';
        $montoCancelacion = '0';
        $resultado = '';
        $validacion = '0';

        $monto = (string) $monto;
        $idImportacion = (string) $idImportacion;
        $fechaPago = (string) $fechaPago;
        $referencia = (string) $referencia;
        $empresa = (string) $empresa;
        $cuentaBancaria = (string) $cuentaBancaria;
        $usuario = (string) $usuario;
        $identificador = (string) $identificador;
        $moneda = (string) $moneda;

        $renExcelVal = $renExcel === null ? '' : (string) $renExcel;
        $renglonVal = $renglon === null ? '' : (string) $renglon;
        $noPagosVal = $noPagos === null ? '' : (string) $noPagos;

        $sql = "BEGIN PKG_ImportaPagoSOF.spImportaPagoSOF(
            TO_DATE(:p_fecha, 'YYYY/MM/DD HH24:MI:SS'),
            :p_ref, :p_monto, :p_empresa, :p_cta, :p_user, :p_iden,
            :p_periodo, :p_oper,
            :p_res, :p_montocan,
            :p_renexcel, :p_renglon, :p_nopagos, :p_idimp,
            :p_val, :p_moneda
        ); END;";

        try {
            $stmt = $this->db_activa->prepare($sql);
            $stmt->bindParam(':p_fecha', $fechaPago, PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_ref', $referencia, PDO::PARAM_STR, 120);
            $stmt->bindParam(':p_monto', $monto, PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_empresa', $empresa, PDO::PARAM_STR, 20);
            $stmt->bindParam(':p_cta', $cuentaBancaria, PDO::PARAM_STR, 20);
            $stmt->bindParam(':p_user', $usuario, PDO::PARAM_STR, 50);
            $stmt->bindParam(':p_iden', $identificador, PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_periodo', $periodo, PDO::PARAM_STR, 10);
            $stmt->bindParam(':p_oper', $operacion, PDO::PARAM_STR, 5);
            $stmt->bindParam(':p_res', $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
            $stmt->bindParam(':p_montocan', $montoCancelacion, PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_renexcel', $renExcelVal, PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_renglon', $renglonVal, PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_nopagos', $noPagosVal, PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_idimp', $idImportacion, PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_val', $validacion, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 20);
            $stmt->bindParam(':p_moneda', $moneda, PDO::PARAM_STR, 10);

            $ok = $stmt->execute();
            if (!$ok) {
                $errorInfo = $stmt->errorInfo();
                $mensajeError = $errorInfo[2] ?? ($errorInfo[1] ?? 'execute() retornó false sin detalle');
                return [
                    'success' => false,
                    'resultado' => $mensajeError,
                    'validacion' => -1,
                ];
            }
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'resultado' => $e->getMessage(),
                'validacion' => -1,
            ];
        }

        return [
            'success' => true,
            'resultado' => trim((string) $resultado),
            'validacion' => (int) $validacion,
        ];
    }

    /**
     * Ejecuta el SP de prueba (no modifica tablas). Misma firma que spImportaPagoSOF.
     * SOLO PRUEBAS: usar cuando APLICAR_PAGOS_SOLO_FLUJO = true.
     *
     * @param string $fechaPago
     * @param string $referencia
     * @param string $monto
     * @param string $empresa
     * @param string $cuentaBancaria
     * @param string $usuario
     * @param string $identificador
     * @param int $renExcel
     * @param int $renglon
     * @param int $noPagos
     * @param int $idImportacion
     * @param string $moneda
     * @return array ['success' => bool, 'resultado' => string, 'validacion' => int]
     */
    public function spImportaPagoSOFPrueba($fechaPago, $referencia, $monto, $empresa, $cuentaBancaria, $usuario, $identificador, $renExcel, $renglon, $noPagos, $idImportacion, $moneda = 'MN')
    {
        $periodo = 1;
        $operacion = 'I';
        $montoCancelacion = 0;
        $resultado = '';
        $validacionStr = '0';

        $sql = "BEGIN PKG_ImportaPagoSOF_PRUEBA.spImportaPagoSOF(TO_DATE(:p_fecha, 'YYYY/MM/DD HH24:MI:SS'), :p_ref, :p_monto, :p_empresa, :p_cta, :p_user, :p_iden, :p_periodo, :p_oper, :p_res, :p_montocan, :p_renexcel, :p_renglon, :p_nopagos, :p_idimp, :p_val, :p_moneda); END;";
        $stmt = $this->db_activa->prepare($sql);
        $stmt->bindParam(':p_fecha', $fechaPago, PDO::PARAM_STR);
        $stmt->bindParam(':p_ref', $referencia, PDO::PARAM_STR);
        $stmt->bindParam(':p_monto', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':p_empresa', $empresa, PDO::PARAM_STR);
        $stmt->bindParam(':p_cta', $cuentaBancaria, PDO::PARAM_STR);
        $stmt->bindParam(':p_user', $usuario, PDO::PARAM_STR);
        $stmt->bindParam(':p_iden', $identificador, PDO::PARAM_STR);
        $stmt->bindParam(':p_periodo', $periodo, PDO::PARAM_STR);
        $stmt->bindParam(':p_oper', $operacion, PDO::PARAM_STR);
        $stmt->bindParam(':p_res', $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 500);
        $stmt->bindParam(':p_montocan', $montoCancelacion, PDO::PARAM_INT);
        $stmt->bindParam(':p_renexcel', $renExcel, PDO::PARAM_INT);
        $stmt->bindParam(':p_renglon', $renglon, PDO::PARAM_INT);
        $stmt->bindParam(':p_nopagos', $noPagos, PDO::PARAM_INT);
        $stmt->bindParam(':p_idimp', $idImportacion, PDO::PARAM_INT);
        $stmt->bindParam(':p_val', $validacionStr, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 22);
        $stmt->bindParam(':p_moneda', $moneda, PDO::PARAM_STR);

        try {
            $ok = $stmt->execute();
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'resultado' => $e->getMessage(),
                'validacion' => -1,
            ];
        }

        if (!$ok) {
            $errorInfo = $stmt->errorInfo();
            $mensajeError = $errorInfo[2] ?? ($errorInfo[1] ?? 'execute() retornó false sin detalle');
            return [
                'success' => false,
                'resultado' => $mensajeError,
                'validacion' => -1,
            ];
        }

        return [
            'success' => (bool) $ok,
            'resultado' => $resultado !== '' ? $resultado : 'OK PRUEBA (sin cambios en BD)',
            'validacion' => (int) $validacionStr,
        ];
    }

    /**
     * Ejecuta spRedistribucionPagos (conciliación de pagos). Misma firma que el SP en Oracle.
     *
     * @param string $empresa
     * @param string $cdgclns
     * @param string $ciclo
     * @param string $tipo
     * @param string $fecha Y-m-d
     * @param int $periodo
     * @param string $secuencia
     * @param float $monto
     * @param string $cuenta
     * @param string $usuario
     * @param string $identificador
     */
    public function spRedistribucionPagos($empresa, $cdgclns, $ciclo, $tipo, $fecha, $periodo, $secuencia, $monto, $cuenta, $usuario, $identificador)
    {
        // En VB6 el identificador se pasa como literal numérico (sin comillas).
        // Para replicar ese comportamiento, lo convertimos con TO_NUMBER().
        $sql = "BEGIN spRedistribucionPagos(:empresa, :cdgclns, :ciclo, :tipo, TO_DATE(:fecha, 'YYYY-MM-DD'), :periodo, :secuencia, :monto, :cuenta, :usuario, TO_NUMBER(:identificador)); END;";
        $stmt = $this->db_activa->prepare($sql);
        $stmt->execute([
            'empresa' => $empresa,
            'cdgclns' => $cdgclns,
            'ciclo' => $ciclo,
            'tipo' => $tipo,
            'fecha' => $fecha,
            'periodo' => $periodo,
            'secuencia' => $secuencia,
            'monto' => $monto,
            'cuenta' => $cuenta,
            'usuario' => $usuario,
            'identificador' => $identificador,
        ]);
    }

    /**
     * Ejecuta spRedistribucionPagos_PRUEBA (solo flujo, no modifica tablas).
     * SOLO PRUEBAS: usar cuando CONCILIACION_SOLO_FLUJO = true.
     *
     * @param string $empresa
     * @param string $cdgclns
     * @param string $ciclo
     * @param string $tipo
     * @param string $fecha Y-m-d
     * @param int $periodo
     * @param string $secuencia
     * @param float $monto
     * @param string $cuenta
     * @param string $usuario
     * @param string $identificador
     */
    public function spRedistribucionPagosPrueba($empresa, $cdgclns, $ciclo, $tipo, $fecha, $periodo, $secuencia, $monto, $cuenta, $usuario, $identificador)
    {
        $sql = "BEGIN spRedistribucionPagos_PRUEBA(:empresa, :cdgclns, :ciclo, :tipo, TO_DATE(:fecha, 'YYYY-MM-DD'), :periodo, :secuencia, :monto, :cuenta, :usuario, TO_NUMBER(:identificador)); END;";
        $stmt = $this->db_activa->prepare($sql);
        $stmt->execute([
            'empresa' => $empresa,
            'cdgclns' => $cdgclns,
            'ciclo' => $ciclo,
            'tipo' => $tipo,
            'fecha' => $fecha,
            'periodo' => $periodo,
            'secuencia' => $secuencia,
            'monto' => $monto,
            'cuenta' => $cuenta,
            'usuario' => $usuario,
            'identificador' => $identificador,
        ]);
    }

    public function queryProcedureDeletePago($cdgns_, $fecha_, $user_, $secuencia_)
    {

        $fecha_parseada = strtotime($fecha_);
        $fecha_parseada = date('d-m-Y', $fecha_parseada);


        $empresa = "EMPFIN";
        $fecha = $fecha_parseada;
        $fecha_aux = '';
        $cdgns = $cdgns_;
        $ciclo = "";
        $secuencia = $secuencia_;
        $nombre = "";
        $cdgocpe = "";
        $ejecutivo = "";
        $cdgpe = $user_;
        $monto = "";
        $tipo_mov = "P";
        $tipo = 3;
        $resultado = "";
        $identifica_app = "";

        $query_text = "CALL SPACCIONPAGODIA_PRUEBA(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        //$query_text = "CALL SPACCIONPAGODIA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $fecha, PDO::PARAM_STR);
        $stmt->bindParam(3, $fecha_aux, PDO::PARAM_STR);
        $stmt->bindParam(4, $cdgns, PDO::PARAM_STR);
        $stmt->bindParam(5, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(6, $secuencia, PDO::PARAM_STR);
        $stmt->bindParam(7, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(8, $cdgocpe, PDO::PARAM_STR);
        $stmt->bindParam(9, $ejecutivo, PDO::PARAM_STR);
        $stmt->bindParam(10, $cdgpe, PDO::PARAM_STR);
        $stmt->bindParam(11, $monto, PDO::PARAM_STR);
        $stmt->bindParam(12, $tipo_mov, PDO::PARAM_STR);
        $stmt->bindParam(13, $tipo, PDO::PARAM_INT, 10);
        $stmt->bindParam(14, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);
        //$stmt->bindParam(15,$identifica_app, PDO::PARAM_STR);


        $result = $stmt->execute();

        if ($result) {
            echo $resultado;
            return $resultado;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    public function queryProcedureActualizaSucursal($n_credito_p, $ciclo_p, $nueva_suc_p)
    {

        $empresa = "EMPFIN";
        $no_credito = $n_credito_p;
        $ciclo = $ciclo_p;
        $nuevaSucursal = $nueva_suc_p;
        $resultado = "";

        $query_text = "CALL SPACTUALIZASUC(?, ?, ?, ?, ?)";
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(4, $nuevaSucursal, PDO::PARAM_STR);
        $stmt->bindParam(5, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);

        $result = $stmt->execute();

        if ($result) {
            //print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
            return $resultado;
            //var_dump($resultado);

        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function queryProcedureInsertGarantias($n_credito_p, $articulo_p, $marca_p, $modelo_p, $serie_p, $factura_p, $usuario_p, $valor_p)
    {

        $empresa = "EMPFIN";
        $no_credito = $n_credito_p;
        $ciclo = '10';
        $articulo = $articulo_p;
        $marca = $marca_p;
        $modelo = $modelo_p;
        $serie = $serie_p;
        $factura = $factura_p;
        $usuario = $usuario_p;
        $valor = $valor_p;
        $tipo_transaccion = '1';
        $resultado = "";
        $query_text = "CALL ESIACOM.SPACCIONGARPREN(?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(4, $articulo, PDO::PARAM_STR);
        $stmt->bindParam(5, $marca, PDO::PARAM_STR);
        $stmt->bindParam(6, $modelo, PDO::PARAM_STR);
        $stmt->bindParam(7, $serie, PDO::PARAM_STR);
        $stmt->bindParam(8, $valor, PDO::PARAM_STR);
        $stmt->bindParam(9, $factura, PDO::PARAM_STR);
        $stmt->bindParam(10, $usuario, PDO::PARAM_STR);
        $stmt->bindParam(11, $tipo_transaccion, PDO::PARAM_STR);
        $stmt->bindParam(12, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);

        $result = $stmt->execute();

        if ($result) {
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
            return "0";
        }
    }
    public function queryProcedureDeleteGarantias($n_credito_p, $secuencia, $tipo_transaccion)
    {
        $empresa = "EMPFIN";
        $no_credito = $n_credito_p;
        $ciclo = $secuencia;
        $articulo = "";
        $marca = "";
        $modelo = "";
        $serie = "";
        $factura = "";
        $usuario = "";
        $valor = "";
        $tipo_transaccion = $tipo_transaccion;
        $resultado = "";
        $query_text = "CALL ESIACOM.SPACCIONGARPREN(?,?,?,?,?,?,?,?,?,?,?,?)
";
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(4, $articulo, PDO::PARAM_STR);
        $stmt->bindParam(5, $marca, PDO::PARAM_STR);
        $stmt->bindParam(6, $modelo, PDO::PARAM_STR);
        $stmt->bindParam(7, $serie, PDO::PARAM_STR);
        $stmt->bindParam(8, $valor, PDO::PARAM_STR);
        $stmt->bindParam(9, $factura, PDO::PARAM_STR);
        $stmt->bindParam(10, $usuario, PDO::PARAM_STR);
        $stmt->bindParam(11, $tipo_transaccion, PDO::PARAM_STR);
        $stmt->bindParam(12, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);

        $result = $stmt->execute();

        if ($result) {
            return $resultado;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
            return "0";
        }
    }

    public function queryProcedureUpdatesGarantias($n_credito_p, $articulo_p, $marca_p, $modelo_p, $serie_p, $factura_p, $usuario_p, $valor_p, $secuencia_p)
    {
        $empresa = "EMPFIN";
        $no_credito = $n_credito_p;
        $secuencia = $secuencia_p;
        $articulo = $articulo_p;
        $marca = $marca_p;
        $modelo = $modelo_p;
        $serie = $serie_p;
        $factura = $factura_p;
        $usuario = $usuario_p;
        $valor = $valor_p;
        $tipo_transaccion = '2';
        $resultado = "";

        $query_text = "CALL ESIACOM.SPACCIONGARPREN(?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3, $secuencia, PDO::PARAM_STR);
        $stmt->bindParam(4, $articulo, PDO::PARAM_STR);
        $stmt->bindParam(5, $marca, PDO::PARAM_STR);
        $stmt->bindParam(6, $modelo, PDO::PARAM_STR);
        $stmt->bindParam(7, $serie, PDO::PARAM_STR);
        $stmt->bindParam(8, $valor, PDO::PARAM_STR);
        $stmt->bindParam(9, $factura, PDO::PARAM_STR);
        $stmt->bindParam(10, $usuario, PDO::PARAM_STR);
        $stmt->bindParam(11, $tipo_transaccion, PDO::PARAM_STR);
        $stmt->bindParam(12, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);

        $result = $stmt->execute();

        if ($result) {
            return $resultado;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
            return "0";
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function queryProcedureActualizaNumCredito($credito_a, $credito_n)
    {

        $empresa = "EMPFIN";
        $credito_actual = $credito_a;
        $credito_nuevo = $credito_n;
        $resultado_s = "";

        $query_text = "CALL SPACTUALIZACODIGOGPO(?,?,?,?)";

        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $credito_actual, PDO::PARAM_STR);
        $stmt->bindParam(3, $credito_nuevo, PDO::PARAM_STR);
        $stmt->bindParam(4, $resultado_s, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);

        $result = $stmt->execute();

        if ($result) {
            var_dump($resultado_s);
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
            return "0";
        }
    }
    public function queryProcedureActualizaNumCreditoCiclo($credito_a, $ciclo_n)
    {
        $empresa = "EMPFIN";
        $credito_actual = $credito_a;
        $ciclo_n = $ciclo_n;
        $resultado_s = "";

        $query_text = "CALL SPACTUALIZACICLOGPO(?,?,?,?)";

        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $credito_actual, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo_n, PDO::PARAM_STR);
        $stmt->bindParam(4, $resultado_s, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);

        $result = $stmt->execute();

        if ($result) {
            echo $resultado_s;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    public function queryProcedureActualizaNumCreditoSituacion($credito_a, $ciclo_n, $situacion)
    {
        $empresa = "EMPFIN";
        $credito_actual = $credito_a;
        $ciclo_n = $ciclo_n;
        $situacion_n = $situacion;
        $resultado_s = "";

        $query_text = "CALL SPACTUALIZASITUACION(?,?,?,?,?)";

        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $credito_actual, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo_n, PDO::PARAM_STR);
        $stmt->bindParam(4, $situacion_n, PDO::PARAM_STR);
        $stmt->bindParam(5, $resultado_s, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);

        $result = $stmt->execute();

        if ($result) {
            echo $resultado_s;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }
}
