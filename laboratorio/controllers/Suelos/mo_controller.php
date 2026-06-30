<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('suelos.mo');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../models/Suelos/mo_model.php';

$resultado = lab_analysis_take_flash();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $campos = [
        'peso_muestra',
        'sulfato_ferroso_consumido',
    ];
    $resultados = [];
    $rowFields = array_merge($campos, ['lote', 'numero_laboratorio']);

    for ($fila = 0, $total = lab_post_row_count($rowFields); $fila < $total; $fila++) {
        if (!lab_post_row_has_data($campos, $fila)) {
            continue;
        }

        $pesoMuestra = lab_post_float('peso_muestra', $fila);
        $sulfatoFerrosoConsumido = lab_post_float('sulfato_ferroso_consumido', $fila);

        // Constantes del método según la hoja de referencia de Materia Orgánica.
        $m1Dicromato = 1.04;
        $m2Dicromato = 1.04;
        $valSolucionFerroso = ($m1Dicromato + $m2Dicromato) / 2;
        $mililitrosUtilSulfatoFerroso1N = 1.0;
        $normalidadDicromatoPotasio = 1.0;
        $dicromatoPotasio = $valSolucionFerroso;
        $normalidadSulfatoFerroso = $dicromatoPotasio != 0
            ? (($mililitrosUtilSulfatoFerroso1N * $normalidadDicromatoPotasio) / $dicromatoPotasio)
            : 0;
        $blancoSulfatoFerroso = 10.50;
        $mlUtilSulfatoFerroso1N = $blancoSulfatoFerroso;
        $dicromatoConsumido = $valSolucionFerroso;
        $porcentajeCarbonoOrganico = $pesoMuestra != 0
            ? ((($mlUtilSulfatoFerroso1N - $sulfatoFerrosoConsumido) * $normalidadSulfatoFerroso * 0.39) / $pesoMuestra)
            : 0;
        $porcentajeMateriaOrganica = $porcentajeCarbonoOrganico * 1.724;

        $resultados[] = guardarMo([
            'peso_muestra' => $pesoMuestra,
            'sulfato_ferroso_consumido' => $sulfatoFerrosoConsumido,
            'porcentaje_carbono_organico' => $porcentajeCarbonoOrganico,
            'porcentaje_materia_organica' => $porcentajeMateriaOrganica,
            'm1_dicromato' => $m1Dicromato,
            'm2_dicromato' => $m2Dicromato,
            'val_solucion_ferroso' => $valSolucionFerroso,
            'normalidad_sulfato_ferroso' => $normalidadSulfatoFerroso,
            'ml_util_sulfato_ferroso1N' => $mlUtilSulfatoFerroso1N,
            'dicromato_potasio' => $dicromatoPotasio,
            'dicromato_consumido' => $dicromatoConsumido,
            'blanco_sulfato_ferroso' => $blancoSulfatoFerroso,
        ]);
    }

    $resultado = lab_resultado_multiple($resultados, 'materia organica');
}

lab_analysis_redirect_after_success($resultado);
require_once __DIR__ . '/../../view/Suelos/mo_view.php';
