<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('aguas.cloruros');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Aguas/cloruros_model.php';

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['ml_muestra', 'ml_agno3_blanco', 'ml_agno3_muestra'];
    $resultados = [];
    $cloruros_mgl = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $ml_muestra = lab_post_float('ml_muestra', $fila);
        $ml_agno3_blanco = lab_post_float('ml_agno3_blanco', $fila);
        $ml_agno3_muestra = lab_post_float('ml_agno3_muestra', $fila);
        $normalidad_agno3 = 0.0141;
        $cloruros_mgl = $ml_muestra != 0
            ? (($ml_agno3_muestra - $ml_agno3_blanco) * $normalidad_agno3 * 35450) / $ml_muestra
            : 0;

        $resultados[] = guardarCloruros(
            $ml_muestra,
            $ml_agno3_blanco,
            $ml_agno3_muestra,
            $normalidad_agno3,
            $cloruros_mgl
        );
    }

    $resultado = lab_resultado_multiple($resultados, 'cloruros');
    $resultado['cloruros_mgl'] = $cloruros_mgl;
}

require_once __DIR__ . '/../../view/Aguas/cloruros_view.php';
?>
