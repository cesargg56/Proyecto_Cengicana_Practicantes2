<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.cc');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Suelos/cc_model.php';

$conexion = new Conexion();
$conn = $conexion->conectar();

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['peso_caja', 'peso_caja_mhumeda', 'peso_caja_mseca', 'no_caja', 'control'];
    $resultados = [];
    $psh = 0;
    $pss = 0;
    $porcentaje_cc = 0;

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
        $porcentaje_cc = ($pss != 0) ? (($psh - $pss) / $pss) * 100 : 0;

        $resultados[] = guardarCC(
            $peso_caja,
            $peso_caja_mhumeda,
            $peso_caja_mseca,
            $psh,
            $pss,
            $porcentaje_cc,
            $no_caja,
            $control
        );
    }

    $resultado = lab_resultado_multiple($resultados, 'capacidad de campo');
    $resultado['psh'] = $psh;
    $resultado['pss'] = $pss;
    $resultado['porcentaje_cc'] = $porcentaje_cc;
}

require_once __DIR__ . '/../../view/Suelos/cc_view.php';
?>
