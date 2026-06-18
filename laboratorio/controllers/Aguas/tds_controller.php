<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('aguas.tds');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Aguas/tds_model.php';

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['lectura_tds'];
    $resultados = [];
    $tds_mgl = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $lectura_tds = lab_post_float('lectura_tds', $fila);
        $tds_mgl = $lectura_tds;
        $resultados[] = guardarTDS($lectura_tds, $tds_mgl);
    }

    $resultado = lab_resultado_multiple($resultados, 'TDS');
    $resultado['tds_mgl'] = $tds_mgl;
}

require_once __DIR__ . '/../../view/Aguas/tds_view.php';
?>
