<?php
require_once __DIR__ . '/../conexion.php';

function guardarMicros($peso, $conc_cu, $conc_zn, $conc_fe, $conc_mn, $conc_k, $blk_cu, $blk_zn, $blk_fe, $blk_mn, $blk_k, $ppm_cu, $ppm_zn, $ppm_fe, $ppm_mn, $ppm_k, $control, array $metadata = [])
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO suelo_micros
        (peso, conc_cu, conc_zn, conc_fe, conc_mn, conc_k, blk_cu, blk_zn, blk_fe, blk_mn, blk_k, ppm_cu, ppm_zn, ppm_fe, ppm_mn, ppm_k, control)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $ok = $stmt->execute([
        $peso, $conc_cu, $conc_zn, $conc_fe, $conc_mn, $conc_k,
        $blk_cu, $blk_zn, $blk_fe, $blk_mn, $blk_k,
        $ppm_cu, $ppm_zn, $ppm_fe, $ppm_mn, $ppm_k, $control,
    ]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Micro Nutrientes guardados correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar.'];
}
?>
