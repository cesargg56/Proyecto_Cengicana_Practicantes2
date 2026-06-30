<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../legacy_analysis_model_helper.php';

function guardarBrixPol($brix, $pol, $peso_torta, $pureza_jugo, $porcentaje_jugo, $rendimiento_comercial_lbs, $rendimiento_comercial_kg, $rendimiento_real_lbs, $rendimiento_real_kg, $porcentaje_pol_cana, array $metadata = [])
{
    $conn = (new Conexion())->conectar();

    $id = labLegacyInsertAnalysisRow($conn, 'cana_brixpol', [
        'brix' => $brix,
        'pol' => $pol,
        'peso_torta' => $peso_torta,
        'pureza_jugo' => $pureza_jugo,
        'porcentaje_jugo' => $porcentaje_jugo,
        'rendimiento_comercial_lbs' => $rendimiento_comercial_lbs,
        'rendimiento_comercial_kg' => $rendimiento_comercial_kg,
        'rendimiento_real_lbs' => $rendimiento_real_lbs,
        'rendimiento_real_kg' => $rendimiento_real_kg,
        'porcentaje_pol_cana' => $porcentaje_pol_cana,
    ], $metadata);

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Brix y Pol guardados correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar.'];
}
?>
