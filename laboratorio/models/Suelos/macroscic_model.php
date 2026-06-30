<?php
require_once __DIR__ . '/../conexion.php';

function guardarMacroscic($peso, $ppm_ca, $ppm_mg, $ppm_k, $ppm_na, $blk_ca, $blk_mg, $blk_k, $blk_na, $meq_ca, $meq_mg, $meq_k, $meq_na, $control, $cic_blanco, $cic_muestra, $cic_meq, array $metadata = [])
{
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO suelo_macros
        (peso, ppm_ca, ppm_mg, ppm_k, ppm_na, blk_ca, blk_mg, blk_k, blk_na, meq_ca, meq_mg, meq_k, meq_na, control, cic_blanco, cic_muestra, cic_meq)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $ok = $stmt->execute([
        $peso, $ppm_ca, $ppm_mg, $ppm_k, $ppm_na,
        $blk_ca, $blk_mg, $blk_k, $blk_na,
        $meq_ca, $meq_mg, $meq_k, $meq_na,
        $control, $cic_blanco, $cic_muestra, $cic_meq,
    ]);
    $id = $ok ? (int) $conn->lastInsertId() : false;

    if ($id !== false) {
        return ['exito' => true, 'mensaje' => 'Macro Nutrientes y Cic guardados correctamente.', 'id' => $id];
    }

    return ['exito' => false, 'mensaje' => 'Error al guardar.'];
}
?>
