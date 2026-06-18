<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.boro');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/Suelos/boro_model.php';
require_once __DIR__ . '/../../models/conexion.php';

$conexion = new Conexion();
$conn = $conexion->conectar();

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['abs_blanco', 'absorbancia', 'control'];
    $resultados = [];
    $ppm_b = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $abs_blanco = lab_post_float('abs_blanco', $fila);
        $absorbancia = lab_post_float('absorbancia', $fila);
        $control = lab_post_float('control', $fila);

        $ppm_b = (($absorbancia - $abs_blanco) * 40 * (100 + 1.408)) / (20 * 100);
        if ($ppm_b < 0) {
            $ppm_b = 0;
        }

        $resultadoFila = guardarBoro($abs_blanco, $absorbancia, $ppm_b, $control);
        $resultados[] = $resultadoFila;

        if (!empty($resultadoFila['exito']) && isset($resultadoFila['id_boro'], $_POST['punto_curva'], $_POST['abs_curva'])) {
            foreach ($_POST['punto_curva'] as $i => $punto) {
                $id_curva = guardarCurvaBoro((float) $punto, (float) ($_POST['abs_curva'][$i] ?? 0));
                if ($id_curva) {
                    relacionarBoroCurva((int) $resultadoFila['id_boro'], $id_curva);
                }
            }
        }
    }

    $resultado = lab_resultado_multiple($resultados, 'boro');
    $resultado['ppm_b'] = $ppm_b;
}

require_once __DIR__ . '/../../view/Suelos/boro_view.php';
?>
