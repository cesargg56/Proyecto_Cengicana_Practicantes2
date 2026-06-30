<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('cana.brixpol');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Cana/brixpol_model.php';

$resultado = lab_analysis_take_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['brix', 'pol', 'peso_torta'];
    $resultados = [];
    $pureza_jugo = 0;
    $porcentaje_jugo = 0;
    $rendimiento_comercial_lbs = 0;
    $rendimiento_comercial_kg = 0;
    $rendimiento_real_lbs = 0;
    $rendimiento_real_kg = 0;
    $porcentaje_pol_cana = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $brix = lab_post_float('brix', $fila);
        $pol = lab_post_float('pol', $fila);
        $peso_torta = lab_post_float('peso_torta', $fila);

        $pureza_jugo = $brix != 0 ? (($pol / $brix) * 100) : 0;
        $porcentaje_jugo = ((500 - $peso_torta) * 100 / 500);
        $rendimiento_comercial_lbs = $pol * 11.8;
        $rendimiento_comercial_kg = $rendimiento_comercial_lbs * 0.5;
        $rendimiento_real_lbs = $pol * 16.44;
        $rendimiento_real_kg = $rendimiento_real_lbs * 0.5;
        $porcentaje_pol_cana = $rendimiento_real_lbs / 20;

        $resultados[] = guardarBrixPol(
            $brix,
            $pol,
            $peso_torta,
            $pureza_jugo,
            $porcentaje_jugo,
            $rendimiento_comercial_lbs,
            $rendimiento_comercial_kg,
            $rendimiento_real_lbs,
            $rendimiento_real_kg,
            $porcentaje_pol_cana
        );
    }

    $resultado = lab_resultado_multiple($resultados, 'brix y pol');
    $resultado['pureza_jugo'] = $pureza_jugo;
    $resultado['porcentaje_jugo'] = $porcentaje_jugo;
    $resultado['rendimiento_comercial_lbs'] = $rendimiento_comercial_lbs;
    $resultado['rendimiento_comercial_kg'] = $rendimiento_comercial_kg;
    $resultado['rendimiento_real_lbs'] = $rendimiento_real_lbs;
    $resultado['rendimiento_real_kg'] = $rendimiento_real_kg;
    $resultado['porcentaje_pol_cana'] = $porcentaje_pol_cana;
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Cana/brixpol_view.php';
?>
