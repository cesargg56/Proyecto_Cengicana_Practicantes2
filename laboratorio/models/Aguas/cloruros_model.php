<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../legacy_analysis_model_helper.php';

function guardarCloruros($ml_muestra, $ml_agno3_blanco, $ml_agno3_muestra, $normalidad_agno3, $cloruros_mgl, array $metadata = [])
{
    $conn = (new Conexion())->conectar();

    $id = labLegacyInsertAnalysisRow($conn, 'agua_cloruros', [
        'ml_muestra' => $ml_muestra,
        'ml_agno3_blanco' => $ml_agno3_blanco,
        'ml_agno3_muestra' => $ml_agno3_muestra,
        'normalidad_agno3' => $normalidad_agno3,
        'cloruros_mgl' => $cloruros_mgl,
    ], $metadata);

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Cloruros guardados correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar.'];
}
?>
