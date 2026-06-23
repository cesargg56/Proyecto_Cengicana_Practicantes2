<?php
require_once __DIR__ . '/../conexion.php';

function guardarHumedad($no_bandeja, $peso_bandeja, $peso_muestra, $peso_bandeja_seca,
        $peso_bandeja_humedad, $porcentaje_humedad) {
    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO cana_humedad (no_bandeja, peso_bandeja, peso_muestra, peso_bandeja_seca, 
        peso_bandeja_humedad, porcentaje_humedad)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    if ($stmt->execute([
        $no_bandeja, $peso_bandeja, $peso_muestra, $peso_bandeja_seca,
        $peso_bandeja_humedad, $porcentaje_humedad
    ])) {
        return ["exito" => true, "mensaje" => "El porcentaje de humedad se guardó correctamente.", "id" => (int) $conn->lastInsertId()];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
