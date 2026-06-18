<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.fosforo');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/Suelos/fosforo_model.php';
require_once __DIR__ . '/../../models/conexion.php';

$conexion = new Conexion();
$conn = $conexion->conectar();

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $campos = ['abs_blanco', 'absorbancia', 'control'];
    $resultados = [];
    $ppm_sol = 0;
    $ppm_p = 0;

    for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $abs_blanco = lab_post_float('abs_blanco', $fila);
        $absorbancia = lab_post_float('absorbancia', $fila);
        $control = lab_post_float('control', $fila);

        $ppm_sol = (($absorbancia - $abs_blanco) / 0.0481);
        if ($ppm_sol < 0) {
            $ppm_sol = 0;
        }

        $ppm_p = $ppm_sol * 5;
        $resultadoFila = guardarFosforo($abs_blanco, $absorbancia, $ppm_sol, $ppm_p, $control);
        $resultados[] = $resultadoFila;

        if (!empty($resultadoFila['exito']) && isset($resultadoFila['id'], $_POST['punto_curva'], $_POST['abs_curva'])) {
            foreach ($_POST['punto_curva'] as $i => $punto) {
                $id_curva = guardarCurvaFosforo((float) $punto, (float) ($_POST['abs_curva'][$i] ?? 0));
                if ($id_curva) {
                    relacionarFosforoCurva((int) $resultadoFila['id'], $id_curva);
                }
            }
        }
    }

    $resultado = lab_resultado_multiple($resultados, 'fosforo');
    $resultado['ppm_sol'] = $ppm_sol;
    $resultado['ppm_p'] = $ppm_p;
}

require_once __DIR__ . '/../../view/Suelos/fosforo_view.php';
?>
