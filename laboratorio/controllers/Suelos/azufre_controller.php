<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.azufre');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/Suelos/azufre_model.php';
require_once __DIR__ . '/../../models/conexion.php';

$conexion = new Conexion();
$conn = $conexion->conectar();

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['abs_blanco', 'absorbancia', 'control'];
    $resultados = [];
    $ppm_so4 = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $abs_blanco = lab_post_float('abs_blanco', $fila);
        $absorbancia = lab_post_float('absorbancia', $fila);
        $control = lab_post_float('control', $fila);

        $ppm_so4 = (($absorbancia * 1) - $abs_blanco) * (25 / 10) * (100 + 1.408 / 100);
        if ($ppm_so4 < 0) {
            $ppm_so4 = 0;
        }

        $resultadoFila = guardarAzufre($abs_blanco, $absorbancia, $ppm_so4, $control);
        $resultados[] = $resultadoFila;

        if (!empty($resultadoFila['exito']) && isset($resultadoFila['id'], $_POST['punto_curva'], $_POST['abs_curva'])) {
            foreach ($_POST['punto_curva'] as $i => $punto) {
                $id_curva = guardarCurvaAzufre((float) $punto, (float) ($_POST['abs_curva'][$i] ?? 0));
                if ($id_curva) {
                    relacionarAzufreCurva((int) $resultadoFila['id'], $id_curva);
                }
            }
        }
    }

    $resultado = lab_resultado_multiple($resultados, 'azufre');
    $resultado['ppm_so4'] = $ppm_so4;
}

require_once __DIR__ . '/../../view/Suelos/azufre_view.php';
?>
