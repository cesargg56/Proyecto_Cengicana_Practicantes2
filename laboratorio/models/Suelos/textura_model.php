<?php

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../legacy_analysis_model_helper.php';

function guardarTexturaSuelo(array $data, array $metadata = []): array
{
    $conn = (new Conexion())->conectar();
    $id = labLegacyInsertAnalysisRow($conn, 'analisis_textura', [
        'porcentaje_hr' => $data['porcentaje_hr'] ?? 0,
        'lectura_1' => $data['lectura_1'] ?? 0,
        'temp_1' => $data['temp_1'] ?? 0,
        'lectura_corregida_1' => $data['lectura_corregida_1'] ?? 0,
        'porcentaje_l_a' => $data['porcentaje_l_a'] ?? 0,
        'lectura_2' => $data['lectura_2'] ?? 0,
        'temp_2' => $data['temp_2'] ?? 0,
        'lectura_corregida_2' => $data['lectura_corregida_2'] ?? 0,
        'total' => $data['total'] ?? 0,
        'porcentaje_arcilla' => $data['porcentaje_arcilla'] ?? 0,
        'porcentaje_limo' => $data['porcentaje_limo'] ?? 0,
        'porcentaje_arena' => $data['porcentaje_arena'] ?? 0,
        'textura' => $data['textura'] ?? '',
    ], $metadata);

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Textura guardada correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar textura.'];
}
