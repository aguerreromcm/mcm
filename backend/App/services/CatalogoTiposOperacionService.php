<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Database;

/**
 * Read-only access to ESIACOM.CAT_TIPO_OPERACION, the single source of truth
 * for payment operation types (codes, labels, families and payment method).
 *
 * The catalog is loaded once per request; screens and queries derive their
 * type lists from it instead of hardcoding codes.
 */
class CatalogoTiposOperacionService
{
    /** Family (NOMBRE_APP) of savings payments. */
    const FAMILIA_AHORRO = 'Ahorro';

    /** Savings adjustments: they have no family but move savings balances. */
    const AJUSTES_AHORRO = ['A', 'E'];

    /** Families a liquidated credit can still receive. */
    const FAMILIAS_LIQUIDADO = ['Multa', 'Multa gestores', 'Pago excedente'];

    /** Payment methods counted as electronic in the Pagos App corte. */
    const METODOS_ELECTRONICOS = ['ELECTRONICO', 'PSC'];

    /** Shared label of the loyalty-campaign discount (same code as DESCUENTO). */
    const ETIQUETA_DESCUENTO_LEALTAD = 'DESCUENTO DE CAMPAÑA POR LEALTAD';
    const CODIGO_DESCUENTO = 'D';

    private static $catalogo = null;

    /**
     * Catalog rows ordered for display: ORDEN first, then code.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function listar()
    {
        if (self::$catalogo !== null) return self::$catalogo;

        $qry = <<<SQL
            SELECT CODIGO, DESCRIPCION, REPETIBLE_CICLO, EN_APP, METODO, NOMBRE_APP, ORDEN, AFECTA_CIERRE
            FROM CAT_TIPO_OPERACION
            ORDER BY ORDEN NULLS LAST, CODIGO
        SQL;

        $filas = (new Database())->queryAll($qry);
        self::$catalogo = is_array($filas) ? $filas : [];
        return self::$catalogo;
    }

    /** @return array<string, string> code => label */
    public static function etiquetas()
    {
        return array_column(self::listar(), 'DESCRIPCION', 'CODIGO');
    }

    public static function existe($codigo)
    {
        return is_string($codigo) && array_key_exists($codigo, self::etiquetas());
    }

    /** @return string[] codes of the savings family plus the savings adjustments */
    public static function codigosAhorro()
    {
        return array_merge(self::codigosFamilia([self::FAMILIA_AHORRO]), self::AJUSTES_AHORRO);
    }

    public static function esAhorro($codigo)
    {
        return in_array($codigo, self::codigosFamilia([self::FAMILIA_AHORRO]), true);
    }

    public static function esElectronico($codigo)
    {
        foreach (self::listar() as $fila) {
            if ($fila['CODIGO'] === $codigo) return in_array($fila['METODO'], self::METODOS_ELECTRONICOS, true);
        }
        return false;
    }

    /** @return string[] codes whose NOMBRE_APP is one of $familias */
    public static function codigosFamilia(array $familias)
    {
        $codigos = [];
        foreach (self::listar() as $fila) {
            if (in_array($fila['NOMBRE_APP'], $familias, true)) $codigos[] = $fila['CODIGO'];
        }
        return $codigos;
    }

    /** @return array<string, string> code => label of types that cannot repeat in a cycle */
    public static function noRepetibles()
    {
        $tipos = [];
        foreach (self::listar() as $fila) {
            if ($fila['REPETIBLE_CICLO'] === 'N') $tipos[$fila['CODIGO']] = $fila['DESCRIPCION'];
        }
        return $tipos;
    }

    /**
     * Options a liquidated credit can still receive, as [{value, text}] for jQuery.
     *
     * @param string[] $adicionales extra codes (e.g. savings adjustments) appended in catalog order
     */
    public static function opcionesLiquidado(array $adicionales = [])
    {
        $codigos = array_merge(self::codigosFamilia(self::FAMILIAS_LIQUIDADO), $adicionales);
        $opciones = [];
        foreach (self::listar() as $fila) {
            if (in_array($fila['CODIGO'], $codigos, true)) {
                $opciones[] = ['value' => $fila['CODIGO'], 'text' => $fila['DESCRIPCION']];
            }
        }
        return $opciones;
    }

    /**
     * <option> tags for a type select.
     *
     * @param string[] $excluir codes left out of this select
     * @param string   $atributoAhorro attribute added to savings options (e.g. a hiding style)
     * @param bool     $descuentoLealtad add the loyalty discount right after DESCUENTO
     */
    public static function opcionesSelect(array $excluir = [], $atributoAhorro = '', $descuentoLealtad = false)
    {
        $ahorro = self::codigosAhorro();
        $html = '';
        foreach (self::listar() as $fila) {
            $codigo = $fila['CODIGO'];
            if (in_array($codigo, $excluir, true)) continue;

            $atributo = $atributoAhorro !== '' && in_array($codigo, $ahorro, true) ? ' ' . $atributoAhorro : '';
            $html .= self::opcion($codigo, $fila['DESCRIPCION'], $atributo);

            if ($descuentoLealtad && $codigo === self::CODIGO_DESCUENTO) {
                $html .= self::opcion($codigo, self::ETIQUETA_DESCUENTO_LEALTAD);
            }
        }
        return $html;
    }

    /** JSON safe to embed inside an inline <script>. */
    public static function json($valor)
    {
        return json_encode($valor, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
    }

    private static function opcion($codigo, $etiqueta, $atributo = '')
    {
        return '<option' . $atributo . ' value="' . htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') . '">'
            . htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') . "</option>\n";
    }
}
