<?php
require_once __DIR__ . '/../conexion.php';

function guardarResistividad($lectura_resistividad) {
    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO agua_resistividad (lectura_resistividad)
        VALUES (?)"
    );

    if ($stmt->execute([$lectura_resistividad])) {
        return ["exito" => true, "mensaje" => "Resistividad guardada correctamente."];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
