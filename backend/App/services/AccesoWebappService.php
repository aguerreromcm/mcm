<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

/**
 * Webapp access administration (Administración > Acceso Webapp).
 *
 * Manages the independent webapp credentials stored in ESIACOM.API_EXT_*:
 * temporary password, unlock, unlink phone, close sessions and access log.
 * Rules: never touches PE.CLAVE nor API_EXT_RETO, never writes API_EXT_ACCESO,
 * never deletes rows, writes UTC timestamps and audits every action in
 * API_EXT_ADMINISTRACION inside the same transaction.
 */
class AccesoWebappService
{
    private const CDGEM = 'EMPFIN';
    private const PERFILES_ADMIN = ['ADMIN'];
    private const ZONA_LOCAL = 'America/Mexico_City';

    private const VIGENCIA_TEMPORAL_HORAS = 72;
    private const LONGITUD_TEMPORAL = 12;
    /** No ambiguous characters (0/O/o, 1/l/I). */
    private const ALFABETO_MAYUSCULAS = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    private const ALFABETO_MINUSCULAS = 'abcdefghijkmnpqrstuvwxyz';
    private const ALFABETO_DIGITOS = '23456789';
    private const ARGON2_OPCIONES = ['memory_cost' => 65536, 'time_cost' => 3, 'threads' => 4];

    private const MAX_DIAS_BITACORA = 31;
    private const MAX_RENGLONES_BITACORA = 1000;

    /** Admitted webapp profiles (UT.CDGTUS). */
    private const SQL_PERFILES = "'OCOF', 'GTOCA', 'GPAGO', 'GREG'";

    /** @var string */
    private static $sqlUsuarios = <<<'SQL'
SELECT
    U.USUARIO,
    U.NOMBRE,
    U.PERFILES,
    CO.NOMBRE AS SUCURSAL,
    U.BLOQUEO_SICAFIN,
    CASE
        WHEN C.USUARIO IS NULL THEN 'SIN_CONTRASENA'
        WHEN C.BLOQUEADO_HASTA_UTC > SYS_EXTRACT_UTC(SYSTIMESTAMP) THEN 'BLOQUEADA'
        WHEN C.CAMBIO_REQUERIDO = 'S' AND C.TEMPORAL_VENCE_UTC <= SYS_EXTRACT_UTC(SYSTIMESTAMP) THEN 'TEMPORAL_VENCIDA'
        WHEN C.CAMBIO_REQUERIDO = 'S' THEN 'TEMPORAL'
        ELSE 'ACTIVA'
    END AS ESTADO_CONTRASENA,
    TO_CHAR(C.BLOQUEADO_HASTA_UTC, 'YYYY-MM-DD HH24:MI:SS') AS BLOQUEADO_HASTA_UTC,
    TO_CHAR(C.TEMPORAL_VENCE_UTC, 'YYYY-MM-DD HH24:MI:SS') AS TEMPORAL_VENCE_UTC,
    TO_CHAR(D.ALTA_UTC, 'YYYY-MM-DD HH24:MI:SS') AS TELEFONO_DESDE_UTC,
    (
        SELECT COUNT(*) FROM ESIACOM.API_EXT_SESION S
        WHERE S.USUARIO = U.USUARIO
          AND S.REVOCADA_UTC IS NULL
          AND S.VENCE_UTC > SYS_EXTRACT_UTC(SYSTIMESTAMP)
    ) AS SESIONES_ACTIVAS,
    TO_CHAR(A.FECHA_UTC, 'YYYY-MM-DD HH24:MI:SS') AS ULTIMO_ACCESO_UTC,
    A.RESULTADO AS ULTIMO_RESULTADO,
    A.VERSION_APP AS ULTIMA_VERSION_APP
FROM (
    SELECT
        PE.CODIGO AS USUARIO,
        PE.CDGCO,
        TRIM(REGEXP_REPLACE(PE.NOMBRE1 || ' ' || PE.NOMBRE2 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE, ' +', ' ')) AS NOMBRE,
        CASE WHEN PE.BLOQUEO IS NULL OR PE.BLOQUEO = 'N' THEN 'N' ELSE 'S' END AS BLOQUEO_SICAFIN,
        (
            SELECT LISTAGG(DISTINCT UT.CDGTUS, ', ') WITHIN GROUP (ORDER BY UT.CDGTUS)
            FROM UT
            WHERE UT.CDGEM = PE.CDGEM AND UT.CDGPE = PE.CODIGO AND UT.CDGTUS IN (__PERFILES__)
        ) AS PERFILES
    FROM PE
    WHERE PE.CDGEM = :cdgem
      AND PE.ACTIVO = 'S'
      AND EXISTS (
          SELECT 1 FROM UT
          WHERE UT.CDGEM = PE.CDGEM AND UT.CDGPE = PE.CODIGO AND UT.CDGTUS IN (__PERFILES__)
      )
) U
LEFT JOIN CO ON CO.CDGEM = :cdgem AND CO.CODIGO = U.CDGCO
LEFT JOIN ESIACOM.API_EXT_CREDENCIAL C ON C.USUARIO = U.USUARIO
LEFT JOIN ESIACOM.API_EXT_DISPOSITIVO D ON D.USUARIO = U.USUARIO AND D.BAJA_UTC IS NULL
OUTER APPLY (
    SELECT X.FECHA_UTC, X.RESULTADO, X.VERSION_APP
    FROM ESIACOM.API_EXT_ACCESO X
    WHERE X.USUARIO = U.USUARIO
    ORDER BY X.FECHA_UTC DESC
    FETCH FIRST 1 ROW ONLY
) A
ORDER BY U.NOMBRE
SQL;

    /** @var string */
    private static $sqlBitacora = <<<'SQL'
SELECT
    TO_CHAR(A.FECHA_UTC, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_UTC,
    A.USUARIO,
    TRIM(REGEXP_REPLACE(PE.NOMBRE1 || ' ' || PE.NOMBRE2 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE, ' +', ' ')) AS NOMBRE,
    A.RESULTADO,
    A.IP,
    A.DISPOSITIVO_NUEVO,
    A.VERSION_APP,
    TRIM(A.NAVEGADOR || ' ' || A.NAVEGADOR_VERSION) AS NAVEGADOR,
    TRIM(A.SISTEMA || ' ' || A.SISTEMA_VERSION) AS SISTEMA,
    A.TIPO_DISPOSITIVO,
    A.MARCA,
    A.MODELO
FROM ESIACOM.API_EXT_ACCESO A
LEFT JOIN PE ON PE.CDGEM = :cdgem AND PE.CODIGO = A.USUARIO
WHERE A.FECHA_UTC >= TO_TIMESTAMP(:inicio, 'YYYY-MM-DD HH24:MI:SS')
  AND A.FECHA_UTC < TO_TIMESTAMP(:fin, 'YYYY-MM-DD HH24:MI:SS')
SQL;

    /** @var string */
    private static $sqlCredencialActual = <<<'SQL'
SELECT USUARIO FROM ESIACOM.API_EXT_CREDENCIAL WHERE USUARIO = :usuario FOR UPDATE
SQL;

    /** @var string */
    private static $sqlGuardarTemporal = <<<'SQL'
MERGE INTO ESIACOM.API_EXT_CREDENCIAL C
USING (SELECT :usuario AS USUARIO FROM DUAL) N
ON (C.USUARIO = N.USUARIO)
WHEN MATCHED THEN UPDATE SET
    C.HASH_CONTRASENA = :hash,
    C.INTENTOS_FALLIDOS = 0,
    C.BLOQUEADO_HASTA_UTC = NULL,
    C.CAMBIO_REQUERIDO = 'S',
    C.TEMPORAL_VENCE_UTC = SYS_EXTRACT_UTC(SYSTIMESTAMP) + NUMTODSINTERVAL(:horas, 'HOUR'),
    C.ACTUALIZADO_UTC = SYS_EXTRACT_UTC(SYSTIMESTAMP)
WHEN NOT MATCHED THEN INSERT
    (USUARIO, HASH_CONTRASENA, INTENTOS_FALLIDOS, ALTA_UTC, ACTUALIZADO_UTC, CAMBIO_REQUERIDO, TEMPORAL_VENCE_UTC)
VALUES
    (N.USUARIO, :hash, 0, SYS_EXTRACT_UTC(SYSTIMESTAMP), SYS_EXTRACT_UTC(SYSTIMESTAMP), 'S',
     SYS_EXTRACT_UTC(SYSTIMESTAMP) + NUMTODSINTERVAL(:horas, 'HOUR'))
SQL;

    /** @var string */
    private static $sqlVenceTemporal = <<<'SQL'
SELECT TO_CHAR(TEMPORAL_VENCE_UTC, 'YYYY-MM-DD HH24:MI:SS') AS VENCE_UTC
FROM ESIACOM.API_EXT_CREDENCIAL WHERE USUARIO = :usuario
SQL;

    /** @var string */
    private static $sqlDesbloquear = <<<'SQL'
UPDATE ESIACOM.API_EXT_CREDENCIAL
SET INTENTOS_FALLIDOS = 0,
    BLOQUEADO_HASTA_UTC = NULL,
    ACTUALIZADO_UTC = SYS_EXTRACT_UTC(SYSTIMESTAMP)
WHERE USUARIO = :usuario
SQL;

    /** @var string */
    private static $sqlBajaDispositivo = <<<'SQL'
UPDATE ESIACOM.API_EXT_DISPOSITIVO
SET BAJA_UTC = SYS_EXTRACT_UTC(SYSTIMESTAMP)
WHERE USUARIO = :usuario AND BAJA_UTC IS NULL
SQL;

    /** @var string */
    private static $sqlRevocarSesiones = <<<'SQL'
UPDATE ESIACOM.API_EXT_SESION
SET REVOCADA_UTC = SYS_EXTRACT_UTC(SYSTIMESTAMP)
WHERE USUARIO = :usuario AND REVOCADA_UTC IS NULL
SQL;

    /** @var string */
    private static $sqlAuditoria = <<<'SQL'
INSERT INTO ESIACOM.API_EXT_ADMINISTRACION (ADMINISTRADOR, USUARIO, ACCION, IP)
VALUES (:administrador, :usuario, :accion, :ip)
SQL;

    /**
     * Only the ADMIN profile may use the panel (checked server-side on every endpoint).
     */
    public static function esAdministrador(?string $perfil): bool
    {
        return in_array(trim((string) $perfil), self::PERFILES_ADMIN, true);
    }

    /**
     * Active PE users with an admitted webapp profile and their access state.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function listarUsuarios(): array
    {
        $sql = str_replace('__PERFILES__', self::SQL_PERFILES, self::$sqlUsuarios);
        $db = self::conectar();
        $filas = self::consultar($db, $sql, ['cdgem' => self::CDGEM]);

        foreach ($filas as &$fila) {
            $fila['BLOQUEADO_HASTA'] = self::aHoraLocal($fila['BLOQUEADO_HASTA_UTC']);
            $fila['TEMPORAL_VENCE'] = self::aHoraLocal($fila['TEMPORAL_VENCE_UTC']);
            $fila['TELEFONO_DESDE'] = self::aHoraLocal($fila['TELEFONO_DESDE_UTC']);
            $fila['ULTIMO_ACCESO'] = self::aHoraLocal($fila['ULTIMO_ACCESO_UTC']);
            $fila['SESIONES_ACTIVAS'] = (int) $fila['SESIONES_ACTIVAS'];
        }
        unset($fila);

        return $filas;
    }

    /**
     * Access log bounded by a local date range (max 31 days) and optionally by user.
     *
     * @return array<string, mixed> Model::Responde shape
     */
    public static function consultarBitacora(string $usuario, string $desde, string $hasta): array
    {
        $usuarioFiltro = null;
        if (trim($usuario) !== '') {
            $usuarioFiltro = self::normalizarUsuario($usuario);
            if ($usuarioFiltro === null) {
                return Model::Responde(false, 'Usuario no válido.');
            }
        }

        $zona = new \DateTimeZone(self::ZONA_LOCAL);
        $ini = \DateTime::createFromFormat('!Y-m-d', trim($desde), $zona);
        $fin = \DateTime::createFromFormat('!Y-m-d', trim($hasta), $zona);
        if (!$ini || !$fin || $ini->format('Y-m-d') !== trim($desde) || $fin->format('Y-m-d') !== trim($hasta)) {
            return Model::Responde(false, 'Indique un rango de fechas válido.');
        }
        if ($ini > $fin) {
            return Model::Responde(false, 'La fecha inicial no puede ser posterior a la final.');
        }
        if ($ini->diff($fin)->days + 1 > self::MAX_DIAS_BITACORA) {
            return Model::Responde(false, 'El rango máximo es de ' . self::MAX_DIAS_BITACORA . ' días.');
        }

        $fin->modify('+1 day');
        $utc = new \DateTimeZone('UTC');
        $prm = [
            'cdgem' => self::CDGEM,
            'inicio' => $ini->setTimezone($utc)->format('Y-m-d H:i:s'),
            'fin' => $fin->setTimezone($utc)->format('Y-m-d H:i:s'),
        ];
        $sql = self::$sqlBitacora;
        if ($usuarioFiltro !== null) {
            $sql .= ' AND A.USUARIO = :usuario';
            $prm['usuario'] = $usuarioFiltro;
        }
        $sql .= ' ORDER BY A.FECHA_UTC DESC FETCH FIRST ' . self::MAX_RENGLONES_BITACORA . ' ROWS ONLY';

        try {
            $filas = self::consultar(self::conectar(), $sql, $prm);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'No se pudo consultar la bitácora.', null, $e->getMessage());
        }
        foreach ($filas as &$fila) {
            $fila['FECHA'] = self::aHoraLocal($fila['FECHA_UTC']);
        }
        unset($fila);

        $mensaje = count($filas) >= self::MAX_RENGLONES_BITACORA
            ? 'Se muestran los ' . self::MAX_RENGLONES_BITACORA . ' accesos más recientes; acote el rango para ver el resto.'
            : 'OK';
        return Model::Responde(true, $mensaje, $filas);
    }

    /**
     * Generates a temporary password, stores its Argon2id hash with a forced change and
     * returns it once so the admin can hand it over in person.
     *
     * @return array<string, mixed> Model::Responde shape; datos.contrasena holds the plain temporary password
     */
    public static function asignarTemporal(string $usuario, string $administrador, string $ip): array
    {
        $usuario = self::normalizarUsuario($usuario);
        if ($usuario === null) {
            return Model::Responde(false, 'Usuario no válido.');
        }

        $db = self::conectar();
        try {
            if (!self::existeEnPe($db, $usuario)) {
                return Model::Responde(false, 'El usuario no existe en SICAFIN.');
            }

            $temporal = self::generarTemporal();
            $hash = password_hash($temporal, PASSWORD_ARGON2ID, self::ARGON2_OPCIONES);
            if (!is_string($hash) || strpos($hash, '$argon2id$') !== 0) {
                throw new \RuntimeException('No se pudo generar el hash Argon2id.');
            }

            $db->AutoCommitOff();
            $db->IniciaTransaccion();
            $existia = self::consultar($db, self::$sqlCredencialActual, ['usuario' => $usuario]) !== [];
            self::ejecutar($db, self::$sqlGuardarTemporal, [
                'usuario' => $usuario,
                'hash' => $hash,
                'horas' => self::VIGENCIA_TEMPORAL_HORAS,
            ]);
            $accion = $existia ? 'RESTABLECER_CONTRASENA' : 'ASIGNAR_CONTRASENA';
            self::auditar($db, $administrador, $usuario, $accion, $ip);
            $vence = self::consultar($db, self::$sqlVenceTemporal, ['usuario' => $usuario]);
            $db->ConfirmaTransaccion();

            return Model::Responde(true, 'Contraseña temporal generada.', [
                'usuario' => $usuario,
                'contrasena' => $temporal,
                'vence' => self::aHoraLocal($vence[0]['VENCE_UTC'] ?? null),
            ]);
        } catch (\Throwable $e) {
            self::cancelar($db);
            return Model::Responde(false, 'No se pudo generar la contraseña temporal.', null, $e->getMessage());
        }
    }

    /**
     * Clears the 15-minute lock and failed attempts without changing the password.
     *
     * @return array<string, mixed>
     */
    public static function desbloquear(string $usuario, string $administrador, string $ip): array
    {
        return self::accionConAuditoria(
            $usuario,
            $administrador,
            $ip,
            'DESBLOQUEAR',
            [self::$sqlDesbloquear],
            'El usuario no tiene contraseña asignada.',
            'Usuario desbloqueado.',
            'No se pudo desbloquear al usuario.'
        );
    }

    /**
     * Unlinks the active phone and revokes every open session in one transaction.
     *
     * @return array<string, mixed>
     */
    public static function desligarDispositivo(string $usuario, string $administrador, string $ip): array
    {
        return self::accionConAuditoria(
            $usuario,
            $administrador,
            $ip,
            'DESLIGAR_DISPOSITIVO',
            [self::$sqlBajaDispositivo, self::$sqlRevocarSesiones],
            'El usuario no tiene un teléfono ligado.',
            'Teléfono desligado. El equipo anterior pierde el acceso en 5 minutos como máximo.',
            'No se pudo desligar el teléfono.'
        );
    }

    /**
     * Revokes every open session without unlinking the phone.
     *
     * @return array<string, mixed>
     */
    public static function cerrarSesiones(string $usuario, string $administrador, string $ip): array
    {
        return self::accionConAuditoria(
            $usuario,
            $administrador,
            $ip,
            'CERRAR_SESIONES',
            [self::$sqlRevocarSesiones],
            'El usuario no tiene sesiones abiertas.',
            'Sesiones cerradas. El usuario sale en 5 minutos como máximo.',
            'No se pudieron cerrar las sesiones.'
        );
    }

    /**
     * Runs the given UPDATEs plus the audit row in one transaction. The first statement
     * must affect at least one row; otherwise everything is rolled back.
     *
     * @param string[] $sentencias
     * @return array<string, mixed>
     */
    private static function accionConAuditoria(
        string $usuario,
        string $administrador,
        string $ip,
        string $accion,
        array $sentencias,
        string $mensajeSinEfecto,
        string $mensajeOk,
        string $mensajeError
    ): array {
        $usuario = self::normalizarUsuario($usuario);
        if ($usuario === null) {
            return Model::Responde(false, 'Usuario no válido.');
        }

        $db = self::conectar();
        try {
            $db->AutoCommitOff();
            $db->IniciaTransaccion();
            foreach ($sentencias as $i => $sql) {
                $afectados = self::ejecutar($db, $sql, ['usuario' => $usuario]);
                if ($i === 0 && $afectados === 0) {
                    $db->CancelaTransaccion();
                    return Model::Responde(false, $mensajeSinEfecto);
                }
            }
            self::auditar($db, $administrador, $usuario, $accion, $ip);
            $db->ConfirmaTransaccion();
            return Model::Responde(true, $mensajeOk);
        } catch (\Throwable $e) {
            self::cancelar($db);
            return Model::Responde(false, $mensajeError, null, $e->getMessage());
        }
    }

    private static function auditar(Database $db, string $administrador, string $usuario, string $accion, string $ip): void
    {
        $administrador = strtoupper(trim($administrador));
        if ($administrador === '') {
            throw new \RuntimeException('Sesión sin usuario administrador.');
        }
        self::ejecutar($db, self::$sqlAuditoria, [
            'administrador' => substr($administrador, 0, 6),
            'usuario' => $usuario,
            'accion' => $accion,
            'ip' => substr(trim($ip), 0, 45) ?: null,
        ]);
    }

    private static function existeEnPe(Database $db, string $usuario): bool
    {
        $sql = 'SELECT 1 AS EXISTE FROM PE WHERE CDGEM = :cdgem AND CODIGO = :usuario';
        return self::consultar($db, $sql, ['cdgem' => self::CDGEM, 'usuario' => $usuario]) !== [];
    }

    /**
     * Random temporary password with at least one upper, one lower and one digit.
     */
    private static function generarTemporal(): string
    {
        $grupos = [self::ALFABETO_MAYUSCULAS, self::ALFABETO_MINUSCULAS, self::ALFABETO_DIGITOS];
        $todos = implode('', $grupos);
        $caracteres = [];
        foreach ($grupos as $grupo) {
            $caracteres[] = $grupo[random_int(0, strlen($grupo) - 1)];
        }
        while (count($caracteres) < self::LONGITUD_TEMPORAL) {
            $caracteres[] = $todos[random_int(0, strlen($todos) - 1)];
        }
        for ($i = count($caracteres) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$caracteres[$i], $caracteres[$j]] = [$caracteres[$j], $caracteres[$i]];
        }
        return implode('', $caracteres);
    }

    /**
     * PE.CODIGO is VARCHAR2(6) in uppercase.
     */
    private static function normalizarUsuario(string $usuario): ?string
    {
        $usuario = strtoupper(trim($usuario));
        return preg_match('/^[A-Z0-9]{1,6}$/', $usuario) ? $usuario : null;
    }

    private static function aHoraLocal(?string $utc): ?string
    {
        if ($utc === null || $utc === '') {
            return null;
        }
        $fecha = new \DateTime($utc, new \DateTimeZone('UTC'));
        return $fecha->setTimezone(new \DateTimeZone(self::ZONA_LOCAL))->format('d/m/Y H:i');
    }

    private static function conectar(): Database
    {
        $db = new Database();
        $db->db_activa->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $db;
    }

    private static function cancelar(Database $db): void
    {
        try {
            if ($db->db_activa->inTransaction()) {
                $db->CancelaTransaccion();
            }
        } catch (\Throwable $e) {
            // Nothing else to undo.
        }
    }

    /**
     * Direct PDO read: Database::queryAll echoes errors and breaks JSON responses.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function consultar(Database $db, string $sql, array $prm): array
    {
        $stmt = $db->db_activa->prepare($sql);
        $stmt->execute($prm);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    private static function ejecutar(Database $db, string $sql, array $prm): int
    {
        $stmt = $db->db_activa->prepare($sql);
        $stmt->execute($prm);
        return $stmt->rowCount();
    }
}
