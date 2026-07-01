<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.humedad_residual');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../includes/shared_lot_controls_helper.php';
require_once __DIR__ . '/../../models/Suelos/humedad_residual_model.php';

$resultado = lab_analysis_take_flash();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $campos = ['blanco', 'control', 'NoCaja', 'PesoCaja', 'PesoCajaMseca', 'PesoHumedo'];
    $resultados = [];

    $rowFields = array_merge($campos, ['lote', 'numero_laboratorio']);
    for ($fila = 0, $total = lab_post_row_count($rowFields); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $numeroLaboratorio = lab_post_string('numero_laboratorio', $fila);
        if (labSharedControlKeyFromNumero($numeroLaboratorio) !== null) {
            continue;
        }

        $control = lab_post_float('control', $fila);
        $pesoCaja = lab_post_float('PesoCaja', $fila);
        $pesoCajaMseca = lab_post_float('PesoCajaMseca', $fila);
        $PesoMuestraHumedo = lab_post_float('PesoMuestraHumedo', $fila);
        $pesoCajaMHumeda = $pesoCaja + $PesoMuestraHumedo;
        $porHGrav = $PesoMuestraHumedo != 0 ? ((($pesoCajaMHumeda - $pesoCajaMseca) * 100) / $PesoMuestraHumedo) : 0;

        $resultados[] = guardarHumedadResidualSuelo([
            'Control' => $control,
            'PesoCaja' => $pesoCaja,
            'PesoCajaMHumeda' => $pesoCajaMHumeda,
            'PesoCajaMseca' => $pesoCajaMseca,
            'PesoMuestraHumedo' => $PesoMuestraHumedo,
            'PorHGrav' => $porHGrav,
        ]);
    }

    $resultado = lab_resultado_multiple($resultados, 'humedad gravimetrica');
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Suelos/humedad_residual_view.php';
