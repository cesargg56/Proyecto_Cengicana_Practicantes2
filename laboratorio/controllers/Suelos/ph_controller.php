<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.ph');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/Suelos/ph_model.php';

$resultado = lab_analysis_take_flash();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $campos = ['ph', 'temperatura'];
    $resultados = [];

    $rowFields = array_merge($campos, ['lote', 'numero_laboratorio']);

    for ($fila = 0, $total = lab_post_row_count($rowFields); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $resultados[] = guardarPhSuelo([
            'ph' => lab_post_float('ph', $fila),
            'temperatura' => lab_post_float('temperatura', $fila),
        ]);
    }

    $resultado = lab_resultado_multiple($resultados, 'ph');
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Suelos/ph_view.php';
