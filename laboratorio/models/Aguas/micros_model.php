<?php
require_once __DIR__ . '/../conexion.php';

function guardarMicros( $conc_cu, $conc_zn, $conc_fe, $conc_mn,
                        $blk_cu, $blk_zn, $blk_fe, $blk_mn,
                        $cu_mgl, $zn_mgl, $fe_mgl, $mn_mgl){
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO agua_micros
            (conc_cu, conc_zn, conc_fe, conc_mn,
             blk_cu, blk_zn, blk_fe, blk_mn,
             cu_mgl, zn_mgl, fe_mgl, mn_mgl)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if ($stmt->execute([
        $conc_cu, $conc_zn, $conc_fe, $conc_mn,
        $blk_cu, $blk_zn, $blk_fe, $blk_mn,
        $cu_mgl, $zn_mgl, $fe_mgl, $mn_mgl
    ])) {
        return ["exito" => true, "mensaje" => "Micro Nutrientes guardados correctamente."];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
