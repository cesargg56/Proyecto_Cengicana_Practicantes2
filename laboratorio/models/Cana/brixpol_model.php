<?php
require_once __DIR__ . '/../conexion.php';

function guardarBrixPol($brix, $pol, $peso_torta,
    $pureza_jugo, $porcentaje_jugo, 
    $rendimiento_comercial_lbs, $rendimiento_comercial_kg,
    $rendimiento_real_lbs, $rendimiento_real_kg,
    $porcentaje_pol_cana){

    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO cana_brixpol
            (brix, pol, peso_torta,
            pureza_jugo, porcentaje_jugo, 
            rendimiento_comercial_lbs, rendimiento_comercial_kg,
            rendimiento_real_lbs, rendimiento_real_kg,
            porcentaje_pol_cana)
         VALUES (?, ?, ?, ?, ?, ?, ?, ? , ? ,?)"
    );

    if ($stmt->execute([
        $brix, $pol, $peso_torta,
        $pureza_jugo, $porcentaje_jugo,
        $rendimiento_comercial_lbs, $rendimiento_comercial_kg,
        $rendimiento_real_lbs, $rendimiento_real_kg,
        $porcentaje_pol_cana
    ])) {
        return ["exito" => true, "mensaje" => "Brix y Pol guardados correctamente."];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
