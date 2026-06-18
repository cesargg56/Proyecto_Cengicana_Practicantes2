<?php
require_once __DIR__ . '/../conexion.php';

function guardarPMP($peso_caja, $peso_caja_mhumeda, $peso_caja_mseca, $psh, $pss, $porcentaje_pmp,
$no_caja,$control) {
    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO suelo_pmp (peso_caja, peso_caja_mhumeda, peso_caja_mseca, psh, pss, porcentaje_pmp, 
        no_caja, control)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if ($stmt->execute([
        $peso_caja, $peso_caja_mhumeda, $peso_caja_mseca, $psh, $pss, $porcentaje_pmp, $no_caja, $control
    ])) {
        return ["exito" => true, "mensaje" => "Punto de Marchitez Permanente guardado correctamente."];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
