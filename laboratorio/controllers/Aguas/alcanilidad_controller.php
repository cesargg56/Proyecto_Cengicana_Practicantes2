<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('aguas.alcanilidad');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Aguas/alcanilidad_model.php';

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['ml_h2oso4'];
    $resultados = [];
    $alcanilidad_mgl = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $ml_h2oso4 = lab_post_float('ml_h2oso4', $fila);
        $normalidad_h2oso4 = 0.02;
        $vol_muestra = 100;
        $alcanilidad_mgl = ($ml_h2oso4 * $normalidad_h2oso4 * 50000) / $vol_muestra;

        $resultados[] = guardarAlcanilidad($ml_h2oso4, $normalidad_h2oso4, $vol_muestra, $alcanilidad_mgl);
    }

    $resultado = lab_resultado_multiple($resultados, 'alcalinidad');
    $resultado['alcanilidad_mgl'] = $alcanilidad_mgl;
}

require_once __DIR__ . '/../../view/Aguas/alcanilidad_view.php';
?>
