<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('aguas.resistividad');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Aguas/resistividad_model.php';

$conexion = new Conexion();
$conn = $conexion->conectar();

$resultado = lab_analysis_take_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['lectura_resistividad'];
    $resultados = [];

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $resultados[] = guardarResistividad(lab_post_float('lectura_resistividad', $fila));
    }

    $resultado = lab_resultado_multiple($resultados, 'resistividad');
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Aguas/resistividad_view.php';
?>
