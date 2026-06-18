<?php
require_once __DIR__ . '/../conexion.php';

function guardarNitrogeno($peso, $ml_blanco, $ml_muestra, $porcentaje_nitro, $normalidad, $x_nitrogeno,
$control) {
    $conn = (new Conexion())->conectar();

    $stmt = $conn->prepare(
        "INSERT INTO suelo_nitrogeno (peso, hcl_blanco, hcl_muestra, porcentaje_n, x_nitrogeno)
         VALUES (?, ?, ?, ?, ?)"
    );

    if ($stmt->execute([$peso, $ml_blanco, $ml_muestra, $porcentaje_nitro, $x_nitrogeno])) {
        return ["exito" => true, "mensaje" => "Porcentaje de nitrogeno guardado correctamente."];
    } else {
        return ["exito" => false, "mensaje" => "Error al guardar."];
    }
}
?>
