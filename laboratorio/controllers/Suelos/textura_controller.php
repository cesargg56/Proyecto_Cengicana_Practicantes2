<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.textura');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/Suelos/textura_model.php';

$resultado = lab_analysis_take_flash();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $campos = ['porcentaje_hr', 'lectura_1', 'temp_1', 'lectura_2', 'temp_2'];
    $resultados = [];

    $rowFields = array_merge($campos, ['lote', 'numero_laboratorio']);
    for ($fila = 0, $total = lab_post_row_count($rowFields); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $porcentajeHr = lab_post_float('porcentaje_hr', $fila);
        $lectura1 = lab_post_float('lectura_1', $fila);
        $temp1 = lab_post_float('temp_1', $fila);
        $lectura2 = lab_post_float('lectura_2', $fila);
        $temp2 = lab_post_float('temp_2', $fila);

        $factor = (100 - $porcentajeHr) != 0 ? 200 / (100 - $porcentajeHr) : 0;
        $lecturaCorregida1 = (($temp1 - 60) * 0.2) + $lectura1;
        $porcentajeLA = $lecturaCorregida1 * $factor;
        $lecturaCorregida2 = (($temp2 - 60) * 0.2) + $lectura2;
        $porcentajeArcilla = $lecturaCorregida2 * $factor;
        $porcentajeLimo = $porcentajeLA - $porcentajeArcilla;
        $porcentajeArena = 100 - $porcentajeLA;
        $totalCalculado = $porcentajeArcilla + $porcentajeLimo + $porcentajeArena;

        $resultados[] = guardarTexturaSuelo([
            'porcentaje_hr' => $porcentajeHr,
            'lectura_1' => $lectura1,
            'temp_1' => $temp1,
            'lectura_corregida_1' => $lecturaCorregida1,
            'porcentaje_l_a' => $porcentajeLA,
            'lectura_2' => $lectura2,
            'temp_2' => $temp2,
            'lectura_corregida_2' => $lecturaCorregida2,
            'total' => $totalCalculado,
            'porcentaje_arcilla' => $porcentajeArcilla,
            'porcentaje_limo' => $porcentajeLimo,
            'porcentaje_arena' => $porcentajeArena,
            'textura' => '',
        ]);
    }

    $resultado = lab_resultado_multiple($resultados, 'textura');
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Suelos/textura_view.php';
