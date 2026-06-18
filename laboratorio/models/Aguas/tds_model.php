<?php
require_once __DIR__ . '/../conexion.php';

function guardarTDS($lectura_tds, $tds_mgl) {
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO agua_tds (lectura_tds, tds_mgl)
         VALUES (?, ?)"
    );

    if ($stmt->execute([$lectura_tds, $tds_mgl])) {
        return ["exito" => true, "mensaje" => "TDS guardado correctamente."];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
