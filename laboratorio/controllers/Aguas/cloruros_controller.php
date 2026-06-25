<?php
require_once __DIR__ . '/../../includes/auth.php';
lab_require_analysis_access('aguas.cloruros');

require_once __DIR__ . '/../../includes/analisis_post_helper.php';
require_once __DIR__ . '/../../includes/formulario_revision_helper.php';
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Aguas/cloruros_model.php';

$resultado = null;
$labSkipFooterBaseSave = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = ['lote', 'numero_laboratorio', 'ml_muestra', 'ml_agno3_blanco', 'ml_agno3_muestra'];
    $resultados = [];
    $cloruros_mgl = 0;
    $fecha = trim((string) ($_POST['fecha'] ?? date('Y-m-d')));
    $analista = trim((string) ($_POST['analista'] ?? ''));
    $estadoRevisarId = labFormularioEstadoRevisarId();

    if ($fecha === '' || $analista === '') {
        $resultado = [
            'exito' => false,
            'mensaje' => 'Complete fecha y analista para guardar el registro.',
        ];
    } else {
        for ($fila = 0, $total = lab_post_row_count($campos); $fila < $total; $fila++) {
            if (!lab_post_row_has_data($campos, $fila)) {
                continue;
            }

            $codigoLote = lab_post_string('lote', $fila);
            $numeroLaboratorio = lab_post_string('numero_laboratorio', $fila);
            $ml_muestra = lab_post_float('ml_muestra', $fila);
            $ml_agno3_blanco = lab_post_float('ml_agno3_blanco', $fila);
            $ml_agno3_muestra = lab_post_float('ml_agno3_muestra', $fila);
            $normalidad_agno3 = 0.0141;
            $cloruros_mgl = $ml_muestra != 0
                ? (($ml_agno3_muestra - $ml_agno3_blanco) * $normalidad_agno3 * 35450) / $ml_muestra
                : 0;

            try {
                $conn = (new Conexion())->conectar();
                $useTransaction = !$conn->inTransaction();
                if ($useTransaction) {
                    $conn->beginTransaction();
                }

                $idLote = clorurosObtenerIdLote($conn, $codigoLote);
                if (!$idLote) {
                    throw new RuntimeException('El lote "' . $codigoLote . '" no existe en la base de datos.');
                }

                $idSolicitud = clorurosObtenerIdSolicitud($conn, $idLote);
                $numeroLaboratorioNormalizado = clorurosResolverNumeroLaboratorio($conn, $idLote, $numeroLaboratorio);
                if ($numeroLaboratorioNormalizado === null) {
                    throw new RuntimeException('No se pudo identificar el número de laboratorio para "' . $numeroLaboratorio . '".');
                }

                $idFormulario = clorurosCrearFormulario($conn, $estadoRevisarId, $fecha, $analista);
                $guardado = guardarCloruros(
                    $conn,
                    $idSolicitud,
                    $numeroLaboratorioNormalizado,
                    $idLote,
                    $idFormulario,
                    $ml_muestra,
                    $ml_agno3_blanco,
                    $ml_agno3_muestra,
                    $normalidad_agno3,
                    $cloruros_mgl
                );

                if (!$guardado) {
                    throw new RuntimeException('No se pudo guardar el registro de cloruros.');
                }

                if ($useTransaction) {
                    $conn->commit();
                }

                $resultados[] = ['exito' => true, 'mensaje' => 'Cloruros guardados correctamente.'];
            } catch (Throwable $e) {
                if (isset($conn, $useTransaction) && $useTransaction && $conn->inTransaction()) {
                    $conn->rollBack();
                }

                $resultados[] = ['exito' => false, 'mensaje' => $e->getMessage()];
            }
        }

        $resultado = lab_resultado_multiple($resultados, 'cloruros');
        $resultado['cloruros_mgl'] = $cloruros_mgl;
    }
}

require_once __DIR__ . '/../../view/Aguas/cloruros_view.php';
?>
