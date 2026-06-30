<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.micros');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Suelos/micros_model.php';

$resultado = lab_analysis_take_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = [
        'peso', 'conc_cu', 'conc_zn', 'conc_fe', 'conc_mn', 'conc_k',
        'blk_cu', 'blk_zn', 'blk_fe', 'blk_mn', 'blk_k', 'control',
    ];
    $resultados = [];
    $ppm_cu = 0;
    $ppm_zn = 0;
    $ppm_fe = 0;
    $ppm_mn = 0;
    $ppm_k = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $peso = lab_post_float('peso', $fila);
        $conc_cu = lab_post_float('conc_cu', $fila);
        $conc_zn = lab_post_float('conc_zn', $fila);
        $conc_fe = lab_post_float('conc_fe', $fila);
        $conc_mn = lab_post_float('conc_mn', $fila);
        $conc_k = lab_post_float('conc_k', $fila);
        $blk_cu = lab_post_float('blk_cu', $fila);
        $blk_zn = lab_post_float('blk_zn', $fila);
        $blk_fe = lab_post_float('blk_fe', $fila);
        $blk_mn = lab_post_float('blk_mn', $fila);
        $blk_k = lab_post_float('blk_k', $fila);
        $control = lab_post_float('control', $fila);

        $ppm_cu = $peso != 0 ? (($conc_cu - $blk_cu) * 25) / $peso : 0;
        $ppm_zn = $peso != 0 ? (($conc_zn - $blk_zn) * 25) / $peso : 0;
        $ppm_fe = $peso != 0 ? (($conc_fe - $blk_fe) * 25) / $peso : 0;
        $ppm_mn = $peso != 0 ? (($conc_mn - $blk_mn) * 25) / $peso : 0;
        $ppm_k = $peso != 0 ? (($conc_k - $blk_k) * 25) / $peso : 0;

        $resultados[] = guardarMicros(
            $peso,
            $conc_cu,
            $conc_zn,
            $conc_fe,
            $conc_mn,
            $conc_k,
            $blk_cu,
            $blk_zn,
            $blk_fe,
            $blk_mn,
            $blk_k,
            $ppm_cu,
            $ppm_zn,
            $ppm_fe,
            $ppm_mn,
            $ppm_k,
            $control
        );
    }

    $resultado = lab_resultado_multiple($resultados, 'micros');
    $resultado['ppm_cu'] = $ppm_cu;
    $resultado['ppm_zn'] = $ppm_zn;
    $resultado['ppm_fe'] = $ppm_fe;
    $resultado['ppm_mn'] = $ppm_mn;
    $resultado['ppm_k'] = $ppm_k;
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Suelos/micros_view.php';
?>
