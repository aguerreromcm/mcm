# Pagos — drive operation types from `CAT_TIPO_OPERACION`

Objective: replace hardcoded operation-type lists in the Pagos screens (Administración, Registro, Pagos App) with the `ESIACOM.CAT_TIPO_OPERACION` catalog so the new PSC types (Q, J, K, N) appear and count.
Branch: `feat/pagos-catalogo-tipos` · Delivery: ask-on-risk · Test runner: none in project (functional checks via PHP CLI against the test DB `mcm_pruebas`).

## Specs

- **S1 Request (L1).** "ayudame a revisar todas las vistas dentro de la opción Pagos, para identificar donde se tiene que hacer modificaciones para incluir la nueva tabla con el catálogo de tipos" → implementation authorized in L4 ("Sí, impleméntalo").
- **S2 Type selects come from the catalog.** Labels are `DESCRIPCION`, order is `ORDEN NULLS LAST, CODIGO`. Each select keeps its current exclusions:
  - Administración (`#tipo`, `#tipo_e`, `#tipo_admin`): every type except C; savings types hidden by the existing `$oculto` rule.
  - Registro (`#tipo`, `#tipo_e`): every type except A, C, E, H; savings types hidden by `$oculto`. `#tipo_e` gains O (was missing).
  - Pagos App detail (`#tipo_pago_detalle`): every type except A, C, E, G, H.
  - The "DESCUENTO DE CAMPAÑA POR LEALTAD" option keeps its branch/user condition and stays right after DESCUENTO.
  - Savings types = `NOMBRE_APP = 'Ahorro'` (B, F, N) plus the adjustments A, E.
- **S3 Liquidated-credit subsets come from the catalog.** Families `Multa`, `Multa gestores`, `Pago excedente`; Administración also adds A, E. Fixes Administración `EditarPago` (Z duplicated, M missing).
- **S4 Display maps come from the catalog.** `mapTipos` (description → code) and `$etiquetas_pago` (code → description).
- **S5 Savings payments stay non-editable.** Edit/delete lock applies to `NOMBRE_APP = 'Ahorro'` (adds N) and the savings confirmation in Registro covers the same set.
- **S6 Non-repeatable types in Registro.** `validaSeguro` hides every `REPETIBLE_CICLO = 'N'` type already registered in the current cycle (was S only).
- **S7 Pagos App summary includes the new types (L2).** User, verbatim: "Si, debe incluir los tipos nuevos". Rule: `NVL(PA.TIPO_ORIGINAL, PA.TIPO) IN (SELECT CODIGO FROM CAT_TIPO_OPERACION WHERE EN_APP = 'S')` for validated, total and amount in `GetPagosAppResumen` and `GetPagosAppEjecutivo`; corte totals use `NOMBRE_APP` families (S stays its own column); detail ordering uses `ORDEN`.
- **S8 PSC counts as electronic (L3).** User, verbatim: "Electrónicos". Electronic = `METODO IN ('ELECTRONICO','PSC')`; Z (`OTRO`) and S (no method) stay cash. Known-type check in the corte detail = type exists in the catalog.
- **Out of scope (reported, not changed):** cash receipt lists `IN ('P','Y','M','Z','S','B')` (PSC is electronic, so they stay correct); accounting layout (P/X/G); DEMO copy `Pagos_temporal`; stored procedure `SPACCIONPAGODIA_PRUEBA` hardcodes repeatable types and rejects a second O/Q/J/K/N in a cycle.

## Tasks

| ID | Specs | Route | Status | Commit |
|---|---|---|---|---|
| T1 | S2–S8 | inline | done (26/26 checks, see L6) | fe90fd65 |
| V1 | S1–S8 | verifier | done: S2–S8 PASS, 0 blockers (see L7) | — |

Route evidence: one coherent change around one catalog service; no Writer trigger reason → inline. Risk: item 6 (no automated tests; corte totals) → independent verifier at the end.

## Log

- **L1 (2026-11-21, user, verbatim):** "Perfecto, en lo que se hacen pruebas, ayudame a revisar todas las vistas dentro de la opción Pagos, para identificar donde se tiene que hacer modificaciones para incluir la nueva tabla con el catálogo de tipos"
- **L2 (2026-11-21, user, verbatim):** "Si, debe incluir los tipos nuevos" — answering whether the Pagos App summary must include the new types.
- **L3 (2026-11-21, user, verbatim):** "Electrónicos" — answering whether PSC payments count as cash or electronic in the corte.
- **L4 (2026-11-21, user, verbatim):** "Sí, impleméntalo"
- **L5:** Evidence: test DB app rows (last 180 days) only use TIPO P, X, B, M; `TIPO_ORIGINAL` is set when the cashier edits the type. `X,O,L,F` equals `METODO='ELECTRONICO'`. Catalog `GARANTIA` has no accent, so screens now show the catalog text.
- **L6:** Checks on fe90fd65: functional + differential PHP CLI script against the test DB, 26/26 PASS — service outputs (order, labels with accents, savings/electronic/liquidated/non-repeatable sets, select exclusions, loyalty option placement, hidden attribute only on savings, script-safe JSON); old vs new `GetPagosApp` (58 groups, same values; ties in `ORDER BY FECHA` reorder arbitrarily), `GetPagosAppResumen` (16 cases), `GetPagosAppEjecutivoDetalle` (126 rows), `GetPagosAppEjecutivo` (8 cases) all identical on existing data; electronic flag unchanged for every pre-existing type. SQL check of the family mapping per code (old types keep their column; Q,J → pago, K → multa, N → ahorro). `php -l` clean on 6 files; rendered inline JS of `index()` and `PagosRegistro()` passes `node --check` (old and new). Not covered: browser click-through (session expired) and end-to-end PSC rows (none in the test DB). RDD assess on d50babac..fe90fd65: high (executable change + item 6), review_due true.
- **L7:** Independent verifier on d50babac..fe90fd65: S2–S8 PASS, 0 blockers. Advisories: (1) S7 drops Y/O/S rows registered at the cash desk from Resumen/Ejecutivo — parent re-ran the probe: only cash-desk rows without `FREGISTRO_APP` or folio differ (S, Y, O), which those queries never read; a cashier edit P→D/R/J now counts in TOTAL as well as validated (old TOTAL_PAGOS used `TIPO`, so validated could exceed total); (2) detail ordering now follows `ORDEN` (G/D/R after app types, B before X); (3) A, C, E now count as known types in the corte detail; (4) hiding by code hides both DESCUENTO options once a D exists in the cycle; (5) deployment needs the app user to resolve `CAT_TIPO_OPERACION` unqualified (ESIACOM) and prod `TIPO_OPERACION` reading the catalog; an empty catalog renders empty selects; (6) pre-existing: `CambioOperacion` offers the previous cycle only for M (not K/L/Z).
