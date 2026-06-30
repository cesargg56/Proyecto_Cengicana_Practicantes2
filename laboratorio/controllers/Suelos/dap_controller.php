<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.dap');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/Suelos/dap_model.php';

$resultado = lab_analysis_take_flash();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $campos = ['peso_caja', 'peso_muestra_seca', 'volumen_final'];
    $resultados = [];

    $rowFields = array_merge($campos, ['lote', 'numero_laboratorio']);

    for ($fila = 0, $total = lab_post_row_count($rowFields); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $pesoCaja = lab_post_float('peso_caja', $fila);
        $pesoMuestraSeca = lab_post_float('peso_muestra_seca', $fila);
        $volumenFinal = lab_post_float('volumen_final', $fila);
        $pesoSueloSeco = $pesoMuestraSeca - $pesoCaja;
        $densidad = $volumenFinal != 0 ? ($pesoSueloSeco / $volumenFinal) : 0;

        $resultados[] = guardarDapSuelo([
            'peso_caja' => $pesoCaja,
            'peso_muestra_seca' => $pesoMuestraSeca,
            'volumen_final' => $volumenFinal,
            'peso_suelo_seco' => $pesoSueloSeco,
            'densidad' => $densidad,
        ]);
    }

    $resultado = lab_resultado_multiple($resultados, 'dap');
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Suelos/dap_view.php';
