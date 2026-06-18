<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.nitrogeno');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Suelos/nitrogeno_model.php';

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['peso', 'ml_blanco', 'ml_muestra', 'x_nitrogeno', 'control'];
    $resultados = [];
    $porcentaje_nitro = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $peso = lab_post_float('peso', $fila);
        $ml_blanco = lab_post_float('ml_blanco', $fila);
        $ml_muestra = lab_post_float('ml_muestra', $fila);
        $normalidad = 0.0101;
        $x_nitrogeno = lab_post_float('x_nitrogeno', $fila);
        $control = lab_post_float('control', $fila);

        $porcentaje_nitro = $peso > 0
            ? (($ml_muestra - $ml_blanco) * 0.0099779 * 1.408 / $peso)
            : 0;

        $resultados[] = guardarNitrogeno(
            $peso,
            $ml_blanco,
            $ml_muestra,
            $porcentaje_nitro,
            $normalidad,
            $x_nitrogeno,
            $control
        );
    }

    $resultado = lab_resultado_multiple($resultados, 'nitrogeno');
    $resultado['porcentaje_nitro'] = $porcentaje_nitro;
}

require_once __DIR__ . '/../../view/Suelos/nitrogeno_view.php';
?>
