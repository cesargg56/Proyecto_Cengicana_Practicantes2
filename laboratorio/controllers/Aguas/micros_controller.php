<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('aguas.micros');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Aguas/micros_model.php';

$resultado = lab_analysis_take_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['conc_cu', 'conc_zn', 'conc_fe', 'conc_mn', 'blk_cu', 'blk_zn', 'blk_fe', 'blk_mn'];
    $resultados = [];
    $cu_mgl = 0;
    $zn_mgl = 0;
    $fe_mgl = 0;
    $mn_mgl = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $conc_cu = lab_post_float('conc_cu', $fila);
        $conc_zn = lab_post_float('conc_zn', $fila);
        $conc_fe = lab_post_float('conc_fe', $fila);
        $conc_mn = lab_post_float('conc_mn', $fila);
        $blk_cu = lab_post_float('blk_cu', $fila);
        $blk_zn = lab_post_float('blk_zn', $fila);
        $blk_fe = lab_post_float('blk_fe', $fila);
        $blk_mn = lab_post_float('blk_mn', $fila);

        $cu_mgl = $conc_cu - $blk_cu;
        $zn_mgl = $conc_zn - $blk_zn;
        $fe_mgl = $conc_fe - $blk_fe;
        $mn_mgl = $conc_mn - $blk_mn;

        $resultados[] = guardarMicros(
            $conc_cu,
            $conc_zn,
            $conc_fe,
            $conc_mn,
            $blk_cu,
            $blk_zn,
            $blk_fe,
            $blk_mn,
            $cu_mgl,
            $zn_mgl,
            $fe_mgl,
            $mn_mgl
        );
    }

    $resultado = lab_resultado_multiple($resultados, 'micros de agua');
    $resultado['cu_mgl'] = $cu_mgl;
    $resultado['zn_mgl'] = $zn_mgl;
    $resultado['fe_mgl'] = $fe_mgl;
    $resultado['mn_mgl'] = $mn_mgl;
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Aguas/micros_view.php';
?>
