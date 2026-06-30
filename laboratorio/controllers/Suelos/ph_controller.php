<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.ph');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../includes/shared_lot_controls_helper.php';
require_once __DIR__ . '/../../models/Suelos/ph_model.php';

$resultado = lab_analysis_take_flash();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $campos = ['blanco', 'control', 'ph', 'temperatura'];
    $resultados = [];

    for ($fila = 0, $total = lab_post_row_count(array_merge($campos, ['lote', 'numero_laboratorio'])); $fila < $total; $fila++) {
        if (!lab_post_row_has_data(array_merge($campos, ['lote', 'numero_laboratorio']), $fila)) {
            continue;
        }

        $numeroLaboratorio = lab_post_string('numero_laboratorio', $fila);
        if (labSharedControlKeyFromNumero($numeroLaboratorio) !== null) {
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
