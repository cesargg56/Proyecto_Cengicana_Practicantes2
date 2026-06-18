<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.pmp');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Suelos/pmp_model.php';

$conexion = new Conexion();
$conn = $conexion->conectar();

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['peso_caja', 'peso_caja_mhumeda', 'peso_caja_mseca', 'no_caja', 'control'];
    $resultados = [];
    $psh = 0;
    $pss = 0;
    $porcentaje_pmp = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $peso_caja = lab_post_float('peso_caja', $fila);
        $peso_caja_mhumeda = lab_post_float('peso_caja_mhumeda', $fila);
        $peso_caja_mseca = lab_post_float('peso_caja_mseca', $fila);
        $no_caja = lab_post_string('no_caja', $fila);
        $control = lab_post_float('control', $fila);

        $psh = $peso_caja_mhumeda - $peso_caja;
        $pss = $peso_caja_mseca - $peso_caja;
        $porcentaje_pmp = ($pss != 0) ? (($psh - $pss) / $pss) * 100 : 0;

        $resultados[] = guardarPMP(
            $peso_caja,
            $peso_caja_mhumeda,
            $peso_caja_mseca,
            $psh,
            $pss,
            $porcentaje_pmp,
            $no_caja,
            $control
        );
    }

    $resultado = lab_resultado_multiple($resultados, 'pmp');
    $resultado['psh'] = $psh;
    $resultado['pss'] = $pss;
    $resultado['porcentaje_pmp'] = $porcentaje_pmp;
}

require_once __DIR__ . '/../../view/Suelos/pmp_view.php';
?>
