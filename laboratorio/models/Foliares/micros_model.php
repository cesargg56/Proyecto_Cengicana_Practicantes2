<?php
require_once __DIR__ . '/../conexion.php';

function guardarMicros($peso, $conc_cu, $conc_zn, $conc_fe, $conc_mn,
                        $blk_cu, $blk_zn, $blk_fe, $blk_mn,
                        $ppm_cu, $ppm_zn, $ppm_fe, $ppm_mn, $control){
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO foliar_micros
            (peso, conc_cu, conc_zn, conc_fe, conc_mn,
             blk_cu, blk_zn, blk_fe, blk_mn,
             ppm_cu, ppm_zn, ppm_fe, ppm_mn, control)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if ($stmt->execute([
        $peso, $conc_cu, $conc_zn, $conc_fe, $conc_mn,
        $blk_cu, $blk_zn, $blk_fe, $blk_mn,
        $ppm_cu, $ppm_zn, $ppm_fe, $ppm_mn, $control
    ])) {
        return ["exito" => true, "mensaje" => "Micro Nutrientes guardados correctamente.", "id" => (int) $conn->lastInsertId()];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
