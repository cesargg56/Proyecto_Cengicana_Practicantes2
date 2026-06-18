<?php
require_once __DIR__ . '/../conexion.php';

function guardarConductividad($lectura_conductividad, $temperatura, $ce) {
    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO agua_conductividad (lectura_conductividad, temperatura, ce)
         VALUES (?, ?, ?)"
    );

    if ($stmt->execute([$lectura_conductividad, $temperatura, $ce])) {
        return ["exito" => true, "mensaje" => "Conductividad Eléctrica de agua guardada correctamente."];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
