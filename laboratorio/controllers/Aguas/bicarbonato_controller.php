<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('aguas.bicarbonato');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Aguas/bicarbonato_model.php';

$resultado = lab_analysis_take_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['ml_acl'];
    $resultados = [];
    $bicarbonatos_mgl = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $ml_acl = lab_post_float('ml_acl', $fila);
        $ml_carbonatos = 0;
        $normalidad_h2oso4 = 0.02;
        $volumen_muestra = 100;
        $bicarbonatos_mgl = ($ml_acl - 2 * $ml_carbonatos) * $normalidad_h2oso4 * 50000 / $volumen_muestra;

        $resultados[] = guardarBicarbonato(
            $ml_acl,
            $ml_carbonatos,
            $normalidad_h2oso4,
            $volumen_muestra,
            $bicarbonatos_mgl
        );
    }

    $resultado = lab_resultado_multiple($resultados, 'bicarbonato');
    $resultado['bicarbonatos_mgl'] = $bicarbonatos_mgl;
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Aguas/bicarbonato_view.php';
?>
